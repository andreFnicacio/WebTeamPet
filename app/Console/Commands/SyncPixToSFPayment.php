<?php

namespace App\Console\Commands;

use App\Helpers\API\Financeiro\DirectAccess\Models\Sale;
use App\Helpers\API\Financeiro\Financeiro;
use App\Models\Clientes;
use App\Pix;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncPixToSFPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:pix-to-sf-payment {period}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Pix transactions to SF payment table when it doesnt sync automatically';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period = $this->argument('period');

        if (!preg_match("^\\d{2}/\\d{4}^", $period)) {
            throw new \Exception("Invalid format, please use m/Y, ie: 11/2022");
        }

        $this->info("Beginning sync at " . Carbon::now()->format("d/m/Y H:i:s"));

        $period = explode("/", $period);
        $beginOfMonth = Carbon::createFromFormat("d/m/Y", "01/$period[0]/$period[1]")->startOfMonth();
        $endOfMonth = $beginOfMonth->copy()->endOfMonth();

        $transactions = Pix::where('status', Pix::STATUS__APPROVED)
            ->whereBetween('created_at', [$beginOfMonth, $endOfMonth]);

        $debug = sprintf(
            "Quantity of Pix transaction by the period of %s is %s",
            implode("/", $period),
            $transactions->count()
        );

        Log::info($debug);
        $this->info($debug);

        $processedWithSuccess = 0;
        $processedWithErrors = [];

        $progress = $this->output->createProgressBar($transactions->count());
        $progress->start();

        foreach ($transactions->get() as $transaction) {
            $progress->advance();

            try {
                $customer = $this->getClientOnSF($transaction->id_cliente);
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                $this->info($e->getMessage());
                continue;
            }

            $sale = $this->searchPaymentOnSF($transaction, $customer);

            if ($sale) {
                $this->info(
                    sprintf("\nPix transaction ID: %s already exist in SF Payment table", $transaction->id)
                );
                continue;
            }

            try {
                $this->createPaymentOnSF($transaction, $customer);
                $processedWithSuccess++;
            } catch (\Exception $e) {
                $processedWithErrors[] = $transaction;
                $this->error($e->getMessage());
            }
        }

        $this->info("\n");

        if ($processedWithSuccess > 0) {
            $this->info($processedWithSuccess . " pix transactions processed successfully!");
        }

        if (!empty($processedWithErrors)) {
            $this->error(
                count($processedWithErrors) . " pix transactions not processed. Check the log for more details."
            );
        }

        if ($processedWithSuccess == 0 && empty($processedWithErrors)) {
            $this->info("No transaction processed. All synced to SF :)");
        }

        $progress->finish();
    }

    /**
     * Check if the pix transaction were already saved into SF
     *
     * @param Pix $transaction
     * @param $customer
     * @return Sale|null
     */
    private function searchPaymentOnSF(Pix $transaction, $customer): ?Sale
    {
        return Sale::where('customer_id', $customer->id)
            ->where('acquirer_transaction_id', $transaction->transaction_id)
            ->first();
    }

    /**
     * Create the missing payment on SF
     *
     * @param Pix $transaction
     * @param $customer
     * @return Sale
     * @throws \Exception
     */
    private function createPaymentOnSF(Pix $transaction, $customer)
    {
        $sale = new Sale();
        $sale->pix(
            $customer->id,
            $transaction->amountAsMoney,
            Carbon::createFromTimeString($transaction->created_at)->format('m/Y'),
            $transaction->transaction_id,
            '',
            ['guia:' . $transaction->order_id]
        );

        $sale->due_date = Carbon::createFromTimeString($transaction->created_at);
        $sale->paid_at = Carbon::createFromTimeString($transaction->updated_at);

        try {
            $sale->save();
            return $sale;
        } catch (\Exception $e) {
            Log::error("Unable to create pix payment on SF: " . $sale->toJson());
            throw new \Exception(
                sprintf(
                    "Unable to create pix payment on SF: %s  | Transaction Data: %s",
                    $e->getMessage(),
                    $sale->toJson()
                )
            );
        }

    }

    /**
     * @throws \Exception
     */
    private function getClientOnSF($customerId)
    {
        $erpCustomer = Clientes::where('id', $customerId)->first();

        if (!$erpCustomer) {
            throw new \Exception(sprintf("Customer with ERP ID %s not found", $customerId));
        }

        $sfService = new Financeiro();
        $customer = $sfService->customerByRefcode($erpCustomer->id_externo);

        if (!$customer) {
            throw new \Exception(
                sprintf(
                    "Customer with ERP ID %s and EXTERNAL ID %s not found on SF",
                    $customerId,
                    $customer->id_externo
                )
            );
        }

        return $customer;
    }
}
