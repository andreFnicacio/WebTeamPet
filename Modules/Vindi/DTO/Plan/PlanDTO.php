<?php

namespace Modules\Vindi\DTO\Plan;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PlanDTO extends FlexibleDataTransferObject
{
    public int $id;
    public string $name;
    public string $interval;
    public int $interval_count;
    public string $billing_trigger_type;
    public int $billing_trigger_day;
    public ?int $billing_cycles;
    public ?string $code;
    public ?string $description;
    public string $status;
    public int $installments;
    public bool $invoice_split;
    public string $interval_name;
    public string $created_at;
    public string $updated_at;
    public PlanItemsDTOCollection $plan_items;
    public ?PlanMetadata $metadata;
}