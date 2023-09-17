<?php

namespace Modules\Vindi\Console;

use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Subscriptions\Services\SubscriptionService;
use Modules\Vindi\Helper\Data;
use Modules\Vindi\Services\VindiService;

class SyncFinancialHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'vindi:sync-financial-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create entries on ERP financial history for charges and payments from Vindi';

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var VindiService
     */
    private $financialService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SubscriptionService $subscriptionService, VindiService $financialService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
        $this->financialService = $financialService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $customers = $this->getActiveCustomers();

        $this->logOutput("Beginning Financial History sync...");

        foreach ($customers as $customer) {
            $customerModel = Clientes::where('id', $customer->id_cliente)->first();

            $customerPets = $customerModel->pets()->get();

            /** @var Pets $pet */
            foreach ($customerPets as $pet) {

                $subscriptionModel = $pet->getLastValidSubscription();

                if (!$subscriptionModel) {
                    continue;
                }

                if (!$subscriptionModel->financial_id) {
                    continue;
                }

                $this->logOutput(
                    sprintf(
                        "Syncing financial history for subscription %s from pet %s from client %s",
                        $subscriptionModel->id,
                        $pet->id,
                        $customerModel->id
                    )
                );

                try {
                    $this->handleSubscriptionFinancialHistory($subscriptionModel, $customerModel);
                } catch (\Exception $e) {
                    $this->logOutput("Unable to sync subscription: " . $subscriptionModel->id . " | Exception: " . $e->getMessage());
                    continue;
                }
            }
        }
    }

    private function handleSubscriptionFinancialHistory(PetsPlanos $subscriptionModel, Clientes $customerModel)
    {
        try {
            $bill = $this->getFinancialBillFromSubscription($subscriptionModel);
        } catch (\Exception $e) {
            throw $e;
        }

        $paymentLinkUrl = $bill->url ?? null;

        $this->subscriptionService->generateFinancialHistoryRecord(
            $customerModel->id,
            $subscriptionModel,
            $paymentLinkUrl
        );

        $this->info("Financial History synced for customer: " . $customerModel->id);

        if ($bill->status == "paid") {
            $this->subscriptionService->generateChargePayment(
                $subscriptionModel,
                Data::toArray($bill->charges[0])
            );

            $this->info("Charge Payment created for paid subscription: " . $subscriptionModel->id);
        }
    }

    private function getFinancialBillFromSubscription(PetsPlanos $subscriptionModel)
    {
        $subscriptionService = $this->financialService->subscription();

        try {
            $subscription = $subscriptionService->get($subscriptionModel->financial_id);
        } catch (\Exception $e) {
            throw new \Exception("Unable to get subscription from financial service");
        }

        $periodService = $this->financialService->period();

        try {
            $period = $periodService->get($subscription->current_period->id);
        } catch (\Exception $e) {
            throw new \Exception("Unable to get period from financial service");
        }


        $billService = $this->financialService->bills();

        try {
            $bill = $billService->get($period->usages[0]->bill->id);
        } catch (\Exception $e) {
            throw new \Exception("Unable to get period from financial service");
        }

        return $bill;
    }

    private function getActiveCustomers()
    {
        return \DB::select ('SELECT * FROM clientes
            inner join pets on pets.id_cliente = clientes.id
            INNER JOIN `pets_planos` AS `pp` ON `pets`.`id` = `pp`.`id_pet`
            AND pp.id IN
              (SELECT MAX(pp2.id)
               FROM pets_planos AS pp2
               JOIN pets AS p2 ON p2.id = pp2.id_pet
               WHERE pp2.data_encerramento_contrato IS NULL
               GROUP BY p2.id)
               
            INNER JOIN `planos` AS `p` ON `p`.`id` = `pp`.`id_plano`
            
            WHERE clientes.deleted_at IS NULL
            AND clientes.financial_id IS NOT NULL
            group by clientes.cpf
            order by clientes.id desc');
    }

    private function logOutput($message): void
    {
        $this->info($message);
        Log::info($message);
    }
}
