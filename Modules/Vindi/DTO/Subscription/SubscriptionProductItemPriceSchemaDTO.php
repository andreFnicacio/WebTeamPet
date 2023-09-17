<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionProductItemPriceSchemaDTO extends DataTransferObject
{
    public int $id;
    public string $short_format;
    public float $price;
    public ?float $minimum_price;
    public string $schema_type;
    public string $created_at;
}