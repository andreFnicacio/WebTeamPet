<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionCustomerDTO extends DataTransferObject
{
    public int $id;
    public string $name;
    public string $email;
    public string $code;
}