<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionCurrentPeriodDTO extends DataTransferObject
{
    public int $id;
    public string $billing_at;
    public int $cycle;
    public string $start_at;
    public string $end_at;
    public int $duration;
}