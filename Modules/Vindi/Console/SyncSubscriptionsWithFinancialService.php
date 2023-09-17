<?php

namespace Modules\Vindi\Console;

use App\Helpers\Utils;
use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\Resources\PlanResource;
use Modules\Vindi\Services\Resources\SubscriptionResource;
use Modules\Vindi\Services\VindiService;
use Spatie\DataTransferObject\DataTransferObjectError;

class SyncSubscriptionsWithFinancialService extends Command
{
    const PLANS_TO_SYNC = [74, 75, 76, 79];
    const DEFAULT_BILLING_EXPIRATION_DAY = 6;
    const BILLING_EXPIRES_IN = 3;
    const BANK_SLIP_GENERATION_DAY = 20;

    private $paymentMethodsMapper = [
        'cartao' => 'credit_card',
        'boleto' => 'bank_slip_yapay',
        'pix' => 'pix'
    ];

    protected $signature = 'vindi:sync-subscriptions {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local subscriptions to financial service.';

    /**
     * @var VindiService
     */
    private $financialService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(VindiService $financialService)
    {
        parent::__construct();
        $this->financialService = $financialService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $totalSynced = 0;

        $filePath = $this->argument('file');

        if (!file_exists(base_path($filePath))) {
            throw new FileNotFoundException("File not found");
        }

        $customRules = Utils::csvToArray($filePath, ';');

        $customers = $this->getActiveCustomers();

        $this->logOutput("Beginning Subscription Import to Financial Service...");

        foreach ($customers as $customer) {
            $customerModel = Clientes::where('id', $customer->id_cliente)->first();

            $customerPets = $customerModel->pets()->get();

            /** @var Pets $pet */
            foreach ($customerPets as $pet) {
                // We don't need to check for subscriptions on an inactive pet
                if (!$pet->ativo) {
                    $this->logOutput("Pet " . $pet->id . " is inactive, skipping...");
                    continue;
                }

                if ($pet->regime !== Pets::REGIME_MENSAL) {
                    $this->logOutput("Pet " . $pet->id . " annual, skipping...");
                    continue;
                }

                $subscriptionModel = $pet->getMigrationLastValidSubscription();

                if (!$subscriptionModel) {
                    continue;
                }

                if ($subscriptionModel->financial_id) {
                    $this->logOutput("Pet " . $pet->id . " already synced, skipping...");
                    continue;
                }

                $this->logOutput(
                    sprintf(
                        "Syncing subscription %s from pet %s from client %s",
                        $subscriptionModel->id,
                        $pet->id,
                        $customerModel->id
                    )
                );

                try {
                    $this->createFinancialSubscription($subscriptionModel, $customerModel, $customRules);
                } catch (\Exception $e) {
                    $this->logOutput("Unable to sync subscription: " . $subscriptionModel->id . " | Exception: " . $e->getMessage());
                    continue;
                }

                $totalSynced++;
            }
        }
    }

    private function createFinancialSubscription(PetsPlanos $subscription, Clientes $customerModel, array $customRules)
    {
        /** @var SubscriptionResource $subscriptionService */
        $subscriptionService = $this->financialService->subscription();

        /** @var CustomerResource $customerService */
        $customerService = $this->financialService->customer();

        try {
            $financialCustomer = $customerService->get($customerModel->financial_id);
        } catch (\Exception $e) {

            try {
                $financialCustomer = $customerService->find("registry_code=" . $customerModel->cpf);
                $customerModel->financial_id = $financialCustomer->id;
                $customerModel->save();
                $this->logOutput("Financial ID on customer" . $customerModel->id . " was updated");
            } catch (\Exception $e) {
                $this->logOutput("Customer with CPF " . $customerModel->cpf . " not found in financial service");
                throw new \Exception(__("Customer not found in financial service"));
            }

            $this->logOutput("Customer with financial_id " . $customerModel->financial_id . " not found in financial service");
            throw new \Exception(__("Customer not found in financial service"));
        }

        /** @var PlanResource $planService */
        $planService = $this->financialService->plan();

        try {
            $plan = $planService->getByCode($subscription->plano()->first()->id);
        } catch (\Exception $e) {
            throw new \Exception(__("Plan not found in financial service"));
        }

        if (empty($plan)) {
            throw new \Exception(__("Plan not found in financial service"));
        }

        try {
            $subscriptionRequest = $subscriptionService->map($subscription, $financialCustomer, $plan);
            $subscriptionRequest = $this->applyCustomRules($subscriptionRequest, $subscription, $customerModel, $customRules);

        } catch (\Exception $e) {
            throw new DataTransferObjectError($e->getMessage());
        }

        try {

            Log::info("Subscription Request Payload: " . json_encode($subscriptionRequest));

            $financialSubscription = $subscriptionService->createSubscription($subscriptionRequest);

            if ($financialSubscription) {
                $this->updatePetPlans($subscription, $financialSubscription['id']);
                $this->logOutput("Subscription with id: " . $subscription->id . " synced successfully");
                sleep(1);
            }
        } catch (\Exception $e) {
            $this->logOutput("Unable to sync signature: " . $subscription->id . " | Exception: " . $e->getMessage());
        }
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
            
            WHERE `p`.`id` IN (' . implode(",", self::PLANS_TO_SYNC) . ')
            AND clientes.ativo = 1
            AND clientes.deleted_at IS NULL
            AND clientes.financial_id IS NOT NULL
            group by clientes.cpf
            order by clientes.id asc');
    }

    private function updatePetPlans(PetsPlanos $petsPlanos, $financialId): void
    {
        $petsPlanos->update([
            'synced_at' => Carbon::now(),
            'financial_id' => $financialId
        ]);
    }

    private function applyCustomRules(array $subscriptionRequest, PetsPlanos $subscriptionModel, Clientes $customerModel, array $customRules)
    {
        $cyclesWithDiscount = 0;
        foreach ($customRules as $rule) {
            if ($rule['id_pet'] == $subscriptionModel->id_pet) {
                $cyclesWithDiscount = $this->calculateRemainingCycleDiscount($subscriptionModel, $rule['cycles']);
            }
        }

        // Rule to define start_at for bank_slip
        $startAt = Carbon::createFromDate(2023, 01, self::BANK_SLIP_GENERATION_DAY)->toIso8601String();

        // Rule to define start_at for credit_card
        if ($customerModel->forma_pagamento == "cartao") {
            $startAt = $this->handleCreditCardStartAt($customerModel, $subscriptionModel);
        }

        $subscriptionRequest['start_at'] = $startAt;

        $plan = $subscriptionModel->plano()->first();
        $planPrice = $plan->preco_plano_individual;
        $subscriptionPrice = $subscriptionModel->valor_momento;

        $subscriptionRequest['product_items'][0]['pricing_schema']['price'] = $subscriptionPrice;
        $subscriptionRequest['payment_method_code'] = ($customerModel->forma_pagamento) ? $this->parsePaymentMethodCode($customerModel->forma_pagamento) : 'boleto';

        if ($cyclesWithDiscount) {
            $subscriptionRequest['product_items'][0]['pricing_schema']['price'] = $planPrice;
            $subscriptionRequest['product_items'][0]['discounts'][0]['discount_type'] = 'percentage';
            $subscriptionRequest['product_items'][0]['discounts'][0]['percentage'] = 100 - (($subscriptionPrice / $planPrice) * 100);
            $subscriptionRequest['product_items'][0]['discounts'][0]['cycles'] = $cyclesWithDiscount;
        }

        $subscriptionRequest['billing_trigger_type'] = 'beginning_of_period';

        return $subscriptionRequest;
    }

    private function handleCreditCardStartAt($customerModel, $subscriptionModel)
    {
        if (!$customerModel->dia_vencimento) {
            return $subscriptionModel->created_at
                ->subDays(self::BILLING_EXPIRES_IN)
                ->toIso8601String();
        }

        /** Rule to handle February 28 days */
        if ($customerModel->dia_vencimento > 28) {
            return Carbon::createFromDate(2023, 02, 28)
                ->subDays(self::BILLING_EXPIRES_IN)
                ->toIso8601String();
        }

        return Carbon::createFromDate(2023, 02, (int) $customerModel->dia_vencimento)
            ->subDays(self::BILLING_EXPIRES_IN)
            ->toIso8601String();
    }

    private function calculateRemainingCycleDiscount(PetsPlanos $subscriptionModel, int $cycles)
    {
        $subscriptionBeginAt = $subscriptionModel->data_inicio_contrato;
        $subscriptionBeginAt->addMonths($cycles);

        return Carbon::now()->diffInMonths($subscriptionBeginAt);
    }

    private function logOutput($message): void
    {
        $this->info($message);
        Log::info($message);
    }

    private function parsePaymentMethodCode($erpPaymentMethod)
    {
        return $this->paymentMethodsMapper[$erpPaymentMethod];
    }
}
