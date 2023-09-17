<?php

namespace Modules\Vindi\Observers;

use App\Models\PetsPlanos;
use Modules\Vindi\Jobs\CreateFinancialSubscription;

class SubscriptionObserver
{
    /**
     * Handle the subscription "created" event.
     *
     * @param PetsPlanos $subscription
     * @return void
     */
    public function created(PetsPlanos $subscription)
    {
        CreateFinancialSubscription::dispatch($subscription);
    }
}