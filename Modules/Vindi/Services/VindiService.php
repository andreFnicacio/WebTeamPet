<?php

namespace Modules\Vindi\Services;

use Modules\Vindi\Services\Resources\BillResource;
use Modules\Vindi\Services\Resources\ChargeResource;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\Resources\PaymentProfileResource;
use Modules\Vindi\Services\Resources\PeriodResource;
use Modules\Vindi\Services\Resources\PlanResource;
use Modules\Vindi\Services\Resources\SubscriptionResource;
use Vindi\Bill;
use Vindi\Charge;
use Vindi\Customer;
use Vindi\PaymentProfile;
use Vindi\Period;
use Vindi\Plan;
use Vindi\Subscription;
use Vindi\WebhookHandler;

class VindiService
{
    const WEBHOOK_SECRET = "";
    const EVENT_SUBSCRIPTION_CANCELED = 'subscription_canceled';
    const EVENT_SUBSCRIPTION_CREATED = 'subscription_created';
    const EVENT_CHARGE_REJECTED = 'charge_rejected';
    const EVENT_BILL_CREATED = 'bill_created';
    const EVENT_BILL_PAID = 'bill_paid';
    const EVENT_PERIOD_CREATED = 'period_created';
    const EVENT_TEST = 'test';

    public $baseUrl;
    public $apiToken;

    public function __construct(string $baseUrl, string $apiToken)
    {
        $this->baseUrl = $baseUrl;
        $this->apiToken = $apiToken;
    }

    /**
     * @return CustomerResource
     */
    public function customer()
    {
        return new CustomerResource(new Customer($this->getAuth()));
    }

    public function subscription()
    {
        return new SubscriptionResource(new Subscription($this->getAuth()));
    }

    public function period()
    {
        return new PeriodResource(new Period($this->getAuth()));
    }

    public function plan()
    {
        return new PlanResource(new Plan($this->getAuth()));
    }

    public function paymentProfile()
    {
        return new PaymentProfileResource(new PaymentProfile($this->getAuth()));
    }

    public function charges()
    {
        return new ChargeResource(new Charge($this->getAuth()));
    }

    public function bills()
    {
        return new BillResource(new Bill($this->getAuth()));
    }

    public function getWebhookHandle()
    {
        return new WebhookHandler();
    }

    private function getAuth()
    {
        return [
            'VINDI_API_KEY' => $this->apiToken,
            'VINDI_API_URI' => $this->baseUrl
        ];
    }
}