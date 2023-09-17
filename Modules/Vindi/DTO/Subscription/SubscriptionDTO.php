<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionDTO extends DataTransferObject
{
    public int $id;
    public string $status;
    public string $start_at;
    public ?string $end_at;
    public string $next_billing_at;
    public ?string $overdue_since;
    public string $code;
    public ?string $cancel_at;
    public string $interval;
    public int $interval_count;
    public string $billing_trigger_type;
    public int $billing_trigger_day;
    public ?string $billing_cycles;
    public int $installments;
    public string $created_at;
    public string $updated_at;
    public SubscriptionCustomerDTO $customer;
    public SubscriptionPlanDTO $plan;
    public ?SubscriptionProductItemDTOCollection $product_items;
    public SubscriptionPaymentMethodDTO $payment_method;
    public SubscriptionCurrentPeriodDTO $current_period;
    public SubscriptionMetadataDTO $metadata;
    public ?SubscriptionPaymentProfileDTO $payment_profile;
    public bool $invoice_split;
}