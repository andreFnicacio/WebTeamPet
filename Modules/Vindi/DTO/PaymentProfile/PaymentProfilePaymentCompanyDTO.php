<?php

namespace Modules\Vindi\DTO\PaymentProfile;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentProfilePaymentCompanyDTO extends DataTransferObject
{
    public ?int $id;
    public ?string $name;
    public ?string $code;
}