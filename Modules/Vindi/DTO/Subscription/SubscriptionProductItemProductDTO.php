<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionProductItemProductDTO extends DataTransferObject
{
    public int $id;
    public string $name;
    public string $code;
}