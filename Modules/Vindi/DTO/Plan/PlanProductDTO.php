<?php

namespace Modules\Vindi\DTO\Plan;

use Spatie\DataTransferObject\DataTransferObject;

class PlanProductDTO extends DataTransferObject
{
    public int $id;
    public string $name;
    public string $code;
    public string $unit;
    public string $status;
    public string $description;
    public string $invoice;
    public string $created_at;
    public string $updated_at;
    public PricingSchemaDTO $pricing_schema;
}