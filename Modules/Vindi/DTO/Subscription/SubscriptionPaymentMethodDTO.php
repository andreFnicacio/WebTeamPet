<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionPaymentMethodDTO extends DataTransferObject
{
    public int $id;
    public string $public_name;
    public string $name;
    public string $code;
    public string $type;
}