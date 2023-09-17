<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class PricingRangeDTO extends DataTransferObject
{
    public int $start_quantity;
    public int $end_quantity;
    public float $price;
    public float $overage_price;
}