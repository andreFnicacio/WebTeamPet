<?php

namespace Modules\Vindi\DTO\PaymentProfile;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentProfilePaymentMethodDTO extends DataTransferObject
{
    public ?int $id;
    public ?string $public_name;
    public ?string $name;
    public ?string $code;
    public ?string $type;
}