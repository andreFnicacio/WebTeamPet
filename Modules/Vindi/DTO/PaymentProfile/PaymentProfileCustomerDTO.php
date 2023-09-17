<?php

namespace Modules\Vindi\DTO\PaymentProfile;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentProfileCustomerDTO extends DataTransferObject
{
    public ?int $id;
    public ?string $name;
    public ?string $email;
    public ?string $code;
}