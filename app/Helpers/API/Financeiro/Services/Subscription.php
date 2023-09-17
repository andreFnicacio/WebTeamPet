<?php

namespace App\Helpers\API\Financeiro\Services;

class Subscription
{
    private $finance;

    public function __construct()
    {
        $this->finance = new Service();
    }

    public function update($subscription_id, \stdClass $payload)
    {
        return $this->finance->post('/payment-subscription/'.$subscription_id, $payload);

    }


    public function create(\stdClass $payload)
    {
        return $this->finance->post('/payment-subscription', $payload);
    }

}