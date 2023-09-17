<?php

namespace Modules\Vindi\DTO\Plan;

use Modules\Vindi\DTO\Subscription\PricingRangesDTOCollection;
use Spatie\DataTransferObject\DataTransferObject;

class PricingSchemaDTO extends DataTransferObject
{
    public int $id;
    public string $short_format;
    public float $price;
    public float $minimum_price;
    public string $schema_type;
    public PricingRangesDTOCollection $pricing_ranges;
    public string $created_at;
}