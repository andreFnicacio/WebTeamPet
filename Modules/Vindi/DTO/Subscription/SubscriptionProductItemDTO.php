<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionProductItemDTO extends DataTransferObject
{
    public int $id;
    public string $status;
    public int $uses;
    public ?string $cycles;
    public int $quantity;
    public string $created_at;
    public string $updated_at;
    public SubscriptionProductItemProductDTO $product;
    public ?SubscriptionProductItemPriceSchemaDTO $pricing_schema;
}