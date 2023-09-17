<?php

namespace Modules\Vindi\Services\Resources;

use App\Models\PetsPlanos;
use Illuminate\Support\Facades\Log;
use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\DTO\PaymentProfile\PaymentProfileDTO;
use Modules\Vindi\DTO\Plan\PlanDTO;
use Modules\Vindi\DTO\Subscription\SubscriptionDTO;
use Modules\Vindi\Helper\Data;
use Psr\Http\Message\ResponseInterface;
use Vindi\Subscription;

class SubscriptionResource extends AbstractResource
{
    public function __construct(Subscription $service)
    {
        parent::__construct($service);
    }
    
    public function createSubscription(array $request)
    {
        try {
            $this->service->create($request);
            $subscription = $this->parseResponse($this->service->getLastResponse());
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }

        return $subscription;
    }

    private function parseResponse(ResponseInterface $response)
    {
        $body = (string) $response->getBody();

        $body = json_decode($body);

        $parsedResponse = [
            'id' => $body->subscription->id,
            'customer' => [
                'name' => $body->subscription->customer->name,
                'email' => $body->subscription->customer->email,
            ],
            'plan' => [
                'name' => $body->subscription->plan->name
            ],
            'bill' => [
                'status' => $body->bill->status
            ]
        ];

        Log::debug("Subscription Response: " . json_encode($body));

        if (property_exists($body, 'bill') && !is_null($body->bill)) {
            $parsedResponse['bill']['amount'] = $body->bill->amount;

            if ($body->bill->charges[0]->payment_method->code === 'pix') {

                if ($body->bill->charges[0]->last_transaction->status === 'waiting') {
                    $parsedResponse['bill']['qrcode_path'] = $body->bill->charges[0]->last_transaction->gateway_response_fields->qrcode_path;
                    $parsedResponse['bill']['qrcode_original_path'] = $body->bill->charges[0]->last_transaction->gateway_response_fields->qrcode_original_path;
                }
            }
        }

        return $parsedResponse;
    }

    public function get(int $id)
    {
        return $this->service->get($id);
    }

    public function put(int $id, array $data)
    {
        return $this->service->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->service->delete($id);
    }

    public function deleteSubscriptionAndBills(int $id)
    {
        return $this->service->apiRequester->request(
            'DELETE',
            $this->service->url($id . "?cancel_bills=true"),
            ['json' => []]
        );
    }

    public function findByPlanAndCustomer($planId, $customerId)
    {
        $subscription = $this->service->all([
            'query' => "plan_id=" . $planId. ' ' . "customer_id=" . $customerId . ' ' . "status=active"
        ]);

        if (!empty($subscription)) {
            return new SubscriptionDTO($this->toArray(current($subscription)));
        }

        return null;
    }

    public function findByCode(string $code)
    {
        $subscription = $this->service->all([
            'query' => "code=" . $code . "status=active"
        ]);

        if (!empty($subscription)) {
            return current($subscription);
        }

        return null;
    }

    public function cancelSubscription(int $id)
    {
        $this->service->delete($id);
    }

    public function map(
        PetsPlanos $subscription,
        CustomerDTO $customerDTO,
        PlanDTO $planDTO,
        array $paymentData = null,
        PaymentProfileDTO $paymentProfile = null
    ) {

        $data = [
            'start_at' => $subscription->data_inicio_contrato->toIso8601String(),
            'plan_id' => $planDTO->id,
            'customer_id' => $customerDTO->id,
            'code' => (string) $subscription->id,
            'product_items' => [
                [
                    'product_id' => $planDTO->plan_items->current()['product']['id'],
                    'pricing_schema' => [
                        'price' => $subscription->valor_momento
                    ]
                ]
            ],
            'metadata' => [
                "pet_id" => $subscription->id_pet,
                "pet_name" => $subscription->pet()->first()->nome_pet
            ],
        ];

        if (!is_null($paymentData)) {
            $data['installments'] = (int) $paymentData['installments'];

            $paymentMethodCode = $paymentData['payment_method_code'];

            if (str_contains($paymentMethodCode, "credit_card")) {
                $paymentMethodCode = "credit_card";
            }

            if (str_contains($paymentMethodCode, "pix")) {
                $paymentMethodCode = "pix";
            }

            $data['payment_method_code'] = $paymentMethodCode;
        } else {
            $data['payment_method_code'] = 'pix';
        }

        if (!is_null($paymentProfile)) {
            $data['payment_method_code'] = $paymentProfile->payment_method->code;
            $data['payment_profile'] = [
                "id" => $paymentProfile->id
            ];
        }

        return $data;
    }
}