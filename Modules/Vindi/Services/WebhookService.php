<?php

namespace Modules\Vindi\Services;

use App\Models\PetsPlanos;
use Modules\Subscriptions\Services\SubscriptionService;
use Modules\Vindi\Helper\Data;

class WebhookService
{
    protected SubscriptionService $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function handleBillPaid(object $data)
    {
        $data = Data::toArray($data);

        if (!$this->isValid($data)) {
            return;
        }

        $status = $data['bill']['status'];

        if ($status !== "paid") {
            return;
        }

        $subscription = $data['bill']['subscription'];

        $financialCharge = $data['bill']['charges'][0];

        $this->subscriptionService->activate($subscription, $financialCharge);
    }

    public function handleBillCreated(object $data)
    {
        $data = Data::toArray($data);

        if (!$this->isValid($data)) {
            return;
        }

        $subscription = $data['bill']['subscription'];

        /** @var PetsPlanos $subscription */
        $subscriptionModel = PetsPlanos::where('id', $subscription['code'])->first();

        $this->subscriptionService->generateFinancialHistoryRecord(
            $data['bill']['customer']['code'],
            $subscriptionModel,
            $data['bill']['url']
        );
    }

    private function isValid($data): bool
    {
        if (isset($data['bill'])) {

            if (!isset($data['bill']['subscription'])) {
                return false;
            }
        }

        return true;
    }
}