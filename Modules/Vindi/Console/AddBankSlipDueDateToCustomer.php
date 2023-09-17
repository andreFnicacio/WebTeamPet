<?php

namespace Modules\Vindi\Console;

use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\VindiService;

class AddBankSlipDueDateToCustomer extends Command
{
    const BANK_SLIP_GENERATION_DAY = 20;
    const PLANS_TO_SYNC = [74, 75, 76, 79];
    const BANK_SLIP_PAYMENT_METHOD_INTERNAL_CODE = "boleto";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'vindi:add-bank-slip-due-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill the metadata bank_slip_due_date into Customer on financial service';

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
        $customers = $this->getActiveCustomers();

        /** @var CustomerResource $customerService */
        $customerService = $this->financialService->customer();

        foreach ($customers as $customer) {
            /** @var Clientes $customerModel */
            $customerModel = Clientes::where('id', $customer->id_cliente)->first();

            if ($customerModel->forma_pagamento == self::BANK_SLIP_PAYMENT_METHOD_INTERNAL_CODE || is_null($customerModel->forma_pagamento)) {

                try {
                    $financialCustomer = $customerService->get($customerModel->financial_id);
                    $this->info("Found customer " . $customerModel->id . " from financial service");
                } catch (\Exception $e) {
                    $this->warn("Customer " . $customerModel->financial_id . " not found on financial service");
                    continue;
                }

                if (!is_null($financialCustomer->metadata) && isset($financialCustomer->metadata['_bank_slip_due_day'])) {
                    $this->info("Client already have bank slip due day set");
                    sleep(1);
                    continue;
                }

                $bankSlipDueDays = $this->calculateDueDays($customerModel);
                $this->info("BankSlipDueDays: " . $bankSlipDueDays);

                try {
                    $customerService->put(
                        $financialCustomer->id,
                        [
                            'metadata' => [
                                '_bank_slip_due_day' => $bankSlipDueDays
                            ]
                        ]
                    );
                    $this->info("Bank Slip Days set to " . $bankSlipDueDays . " successfully for client " . $financialCustomer->id);
                } catch (\Exception $e) {
                    $this->error("Unable to update _bank_slip_due_day: " . $e->getMessage());
                }


                sleep(1);
            }
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
            AND clientes.forma_pagamento IN ("boleto", NULL)
            group by clientes.cpf
            order by clientes.id asc');
    }

    private function calculateDueDays($customerModel)
    {
        $customerDueDay = $customerModel->dia_vencimento;

        if ($customerDueDay == null) {
            /** @var Pets $customerLastPet */
            $customerLastPet = $customerModel->pets()->get()->last();

            /** @var PetsPlanos $subscriptionModel */
            $subscriptionModel = $customerLastPet->getLastValidSubscription();

            $customerDueDay = $subscriptionModel->created_at->day;
        }

        $bankSlipStartDate = Carbon::createFromDate(2023, 01, self::BANK_SLIP_GENERATION_DAY);
        $bankSlipDueDate = Carbon::createFromDate(2023, 02, (int) $customerDueDay);

        $qtyOfDays = $bankSlipStartDate->diffInDays($bankSlipDueDate);
        return (string) ($qtyOfDays > 30) ? 30 : $qtyOfDays;
    }
}
