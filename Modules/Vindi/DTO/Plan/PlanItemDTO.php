<?php

namespace Modules\Vindi\DTO\Plan;

use Spatie\DataTransferObject\DataTransferObject;

class PlanItemDTO extends DataTransferObject
{
    public int $id;
    public PlanProductDTO $product;
    public PricingSchemaDTO $pricing_schema;
    public string $cycles;
    public string $created_at;
    public string $updated_at;
}