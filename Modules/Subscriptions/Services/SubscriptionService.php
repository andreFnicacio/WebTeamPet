<?php

namespace Modules\Subscriptions\Services;

use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Pagamentos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Vindi\Services\VindiService;

class SubscriptionService
{
    protected VindiService $service;

    public function __construct()
    {
        $this->service = app(VindiService::class);
    }

    public function subscribeTo(array $pets, $customerId)
    {
        $defaultStatus = PetsPlanos::STATUS_PRIMEIRO_PLANO;
        $subscriptions = [];
        PetsPlanos::unsetEventDispatcher();
        foreach ($pets as $petId => $pet) {
            $subscriptionData = $pet['subscription'];
            $subscriptionData['id_cliente'] = $customerId;
            $subscriptionData['id_pet'] = $petId;

            $currentSubscription = null;

            if (PetsPlanos::petHasPlan($petId)) {
                $defaultStatus = PetsPlanos::STATUS_ALTERACAO;

                $currentSubscription = PetsPlanos::getCurrentSubscription($petId);

                /** Unsubscribe pet, internally, from its current subscription */
                $this->unsubscribe(
                    $currentSubscription->id,
                    __("SITE/APP | Plano encerrado, pet contratou um novo plano.")
                );

                /** Unsubscribe on financial service */
                $financialSubscriptionService = $this->service->subscription();
                $currentFinancialSubscription = $financialSubscriptionService->findByCode($currentSubscription->id);
                if ($currentFinancialSubscription) {
                    $financialSubscriptionService->cancelSubscription($currentFinancialSubscription->id);
                }
            }

            $subscriptionData['status'] = $defaultStatus;

            $planModel = Planos::where('id', $subscriptionData['id_plano'])->first();

            $subscriptionData['participativo'] = $planModel->participativo;
            $subscriptionData['transicao'] = PetsPlanos::TRANSICAO__NOVA_COMPRA;

            $subscription = PetsPlanos::create($subscriptionData);

            $pet = Pets::find($petId);
            $pet->id_pets_planos = $subscription->id;
            $pet->save();

            $subscriptions[$subscription->id] = $subscription;
        }

        return $subscriptions;
    }

    /**
     * Ends pets_planos subscription
     *
     * @param int $subscriptionId
     * @return void
     */
    public function unsubscribe(int $subscriptionId, string $message, string $cancelAt = null)
    {
        /** @var PetsPlanos $subscription */
        $subscription = PetsPlanos::where('id', $subscriptionId)->first();

        if ($subscription) {
            /** @var Clientes $customer */
            $customer = $subscription->pet()->first()->cliente()->first();
            $customer->addNota($message);

            $subscription->pet()->first()->inactivate();

            $defaultCancelAt = Carbon::now();

            if ($cancelAt) {
                $defaultCancelAt = new Carbon($cancelAt);
            }

            $subscription->setDataEncerramentoContratoAttribute($defaultCancelAt->format('d/m/Y'));
            $subscription->update();
        }
    }

    public function activate(array $data, array $financialCharge)
    {
        /** @var PetsPlanos $subscription */
        $subscription = PetsPlanos::where('id', $data['code'])->first();

        if ($subscription) {
            $subscription->pet()->first()->activate();
            Log::debug(sprintf("Pet %s activated through Webhook", $subscription->pet()->first()->id_pet));

            $this->generateChargePayment($subscription, $financialCharge);
        }
    }

    public function generateChargePayment(PetsPlanos $subscription, array $financialCharge)
    {
        $charge = Cobrancas::where('id_financeiro', '=', $subscription->financial_id)->first();
        Log::debug(sprintf("Charge Payment created for paid subscription: " . json_encode($charge)));
        Log::debug(sprintf("Charge financial Charge: " . json_encode($financialCharge)));
        $paidAt = Carbon::parse($financialCharge['paid_at']);
        $pagamento = Pagamentos::where('id_financeiro', 'PAYMENT-' . null)->first();
        if (!$pagamento) {
            $pagamento = Pagamentos::create([
                    'id_cobranca' => $charge->id,
                    'data_pagamento' => date('Y-m-d'),
                    'complemento' => "Pagamento da assinatura processado pela Vindi",
                    'valor_pago' => $charge->valor_original,
                    'id_financeiro' => null,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                    'forma_pagamento' => 0
            ]);
            Log::debug(sprintf("Create payment VINDI: " . json_encode($pagamento)));
        }
    }


    public function generateFinancialHistoryRecord($customerId, PetsPlanos $subscription, $paymentLinkUrl = null)
    {
        $customer = Clientes::where('id', $customerId)->first();
        /** @var Carbon $createdAt */
        $createdAt = $subscription->created_at;

        // Default expiration day for Financial service
        $expireAt = $createdAt->addDays(3);
        $charge = Cobrancas::cobrancaAutomatica(
            $customer,
            $subscription->valor_momento,
            '',
            $expireAt,
            $createdAt->format('Y-m'),
            $subscription->financial_id,
            false
        );

        if ($paymentLinkUrl) {
            $charge->complemento = $paymentLinkUrl;
        }

        $charge->driver = Cobrancas::DRIVER_VINDI;
        $charge->save();
    }

    public function createCustomer(array $customerData, array $paymentData): Clientes
    {
        Clientes::unsetEventDispatcher();

        if ($paymentData['payment_method_code'] === "credit_card") {
            $customerPaymentMethod = Clientes::FORMA_PAGAMENTO_CARTAO;
        } else {
            $customerPaymentMethod = $paymentData['payment_method_code'];
        }

        $customerModel = Clientes::where('email', $customerData['email'])
            ->where('cpf', $customerData['cpf'])->first();

        $customerData['ativo'] = false;

        if ($customerModel) {
            if ($customerModel::hasActivePet()) {
                $customerData['ativo'] = true;
            }
        }

        $customerData['forma_pagamento'] = $customerPaymentMethod;
        return Clientes::updateOrCreate(
            [
                'email' => $customerData['email'],
                'cpf' => $customerData['cpf']
            ],
            $customerData
        );
    }

    public function createPets(array $petsData, Clientes $customer): array
    {
        $pets = [];
        Pets::unsetEventDispatcher();
        foreach ($petsData as $pet) {

            $pet['ativo'] = false;
            $pet['data_nascimento'] = Carbon::now()->format('d/m/Y');
            $pet['regime'] = $pet['subscription']['regime'];

            /** @var Pets $petModel */
            $petModel = Pets::firstOrCreate(
                ['nome_pet' => $pet['nome_pet'], 'id_cliente' => $customer->id],
                $pet
            );

            // Create temporary microchip if is a new pet
            if (!$petModel->numero_microchip) {
                $petModel->numero_microchip = "PT" . $petModel->id;
                $petModel->save();
            }

            $pets[$petModel->id] = $pet;
        }

        return $pets;
    }

    /**
     * @param array $financialSubscription
     * @return void
     * @throws \Exception
     */
    public function handleSubscriptionCancelling(array $financialSubscription)
    {
        try {
            $subscriptionId = $financialSubscription['id'];
            $this->cancelSubscriptionOnFinancialService($subscriptionId);
            $this->cancelInternalSubscription($subscriptionId);
            Log::debug(
                sprintf("Subscription %s deleted on financial service", $subscriptionId)
            );
        } catch (\Exception $e) {
            Log::error("Unable to cancel subscription on financial service: " . $e->getMessage());
            throw $e;
        }
    }

    public function cancelSubscriptionOnFinancialService(int $subscriptionId): void
    {
        $subscriptionService = $this->service->subscription();
        $subscriptionService->deleteSubscriptionAndBills($subscriptionId);
    }

    public function cancelInternalSubscription(int $financialId)
    {
        /** @var PetsPlanos $subscriptionModel */
        $subscriptionModel = PetsPlanos::where('financial_id', '=', $financialId)->get()->first();

        try {
            $subscriptionModel->delete();

            try {
                /** @var Clientes $customer */
                $customer = Clientes::where('id', '=', $subscriptionModel->pet()->first()->id_cliente)->get()->first();
                $customer->addNota(sprintf(
                    "Assinatura %s do pet %s cancelada por pagamento rejeitado",
                    $subscriptionModel->id,
                    $subscriptionModel->id_pet
                ));
            } catch (\Exception $e) {
                Log::error("Unable to add note to customer while deleting subscription");
            }

        } catch (\Exception $e) {
            Log::error("Unable to delete internal subscription");
            throw $e;
        }

    }
}