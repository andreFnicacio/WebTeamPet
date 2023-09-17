<?php

namespace Modules\Vindi\Jobs;

use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\Resources\PlanResource;
use Modules\Vindi\Services\Resources\SubscriptionResource;
use Modules\Vindi\Services\VindiService;
use Spatie\DataTransferObject\DataTransferObjectError;

class CreateFinancialSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var PetsPlanos
     */
    public $subscription;

    private $paymentMethodsMapper = [
        'cartao' => 'credit_card',
        'boleto' => 'bank_slip_yapay',
        'pix' => 'pix'
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $financialService = app(VindiService::class);

        /** @var SubscriptionResource $subscriptionService */
        $subscriptionService = $financialService->subscription();

        /** @var CustomerResource $customerService */
        $customerService = $financialService->customer();
        $customer = $customerService->getByCode($this->subscription->pet()->first()->cliente()->first()->id);

        if (empty($customer)) {
            throw new \Exception(__("Customer not found in financial service"));
        }

        /** @var PlanResource $planService */
        $planService = $financialService->plan();

        $planModel = Planos::where('id', $this->subscription->plano()->first()->id)->first();

        if ($this->subscription->pet()->first()->regime == Pets::REGIME_MENSAL) {
            $plan = $planService->get($planModel->financial_plan_monthly_id);
        } else {
            $plan = $planService->get($planModel->financial_plan_annual_id);
        }

        if (empty($plan)) {
            throw new \Exception(__("Plan not found in financial service"));
        }

        try {
            $subscriptionRequest = $subscriptionService->map($this->subscription, $customer, $plan);
            $customerModel = Clientes::where('id', $customer->code)->first();

            $subscriptionRequest['payment_method_code'] = ($customerModel->forma_pagamento) ? $this->parsePaymentMethodCode($customerModel->forma_pagamento) : 'boleto';

            try {
                $subscriptionResponse = $subscriptionService->createSubscription($subscriptionRequest);

                $this->subscription->financial_id = $subscriptionResponse['id'];
                PetsPlanos::unsetEventDispatcher();
                $this->subscription->save();

            } catch (\Throwable $e) {
                throw new \Exception($e->getMessage());
            }

        } catch (\Throwable $e) {
            throw new DataTransferObjectError($e->getMessage());
        }
    }

    private function parsePaymentMethodCode($erpPaymentMethod)
    {
        return $this->paymentMethodsMapper[$erpPaymentMethod];
    }
}
