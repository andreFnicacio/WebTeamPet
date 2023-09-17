<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionPlanDTO extends DataTransferObject
{
    public int $id;
    public string $name;
    public string $code;
}