<?php

namespace Modules\Vindi\Services\Resources;

use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\DTO\PaymentProfile\PaymentProfileDTO;
use Vindi\PaymentProfile;

class PaymentProfileResource extends AbstractResource
{
    public function __construct(PaymentProfile $service)
    {
        parent::__construct($service);
    }

    public function getByCustomerId($customerId)
    {
        $profiles = $this->service->all(['query' => "customer_id=" . $customerId . " status=active"]);

        if (is_array($profiles) && !empty($profiles)) {
            return new PaymentProfileDTO($this->toArray(array_pop($profiles)));
        }

        return null;
    }

    public function create(array $request)
    {
        $response = $this->service->create($request);
        return new PaymentProfileDTO($this->toArray($response));
    }

    public function findOrCreate(CustomerDTO $customer, array $payment): PaymentProfileDTO
    {
        if (array_key_exists('payment_profile_id', $payment) && !empty($payment['payment_profile_id'])) {
            $paymentProfile = $this->service->get($payment['payment_profile_id']);
            return new PaymentProfileDTO($this->toArray($paymentProfile));
        }

        if (array_key_exists('gateway_token', $payment) && !empty($payment['gateway_token'])) {

            $paymentMethodCode = $payment['payment_method_code'];

            if (str_contains($payment['payment_method_code'], 'credit_card')) {
                $paymentMethodCode = 'credit_card';
            }

            return $this->create([
                "gateway_token" => $payment['gateway_token'],
                "customer_id" => $customer->id,
                "payment_method_code" => $paymentMethodCode
            ]);
        }
    }
}