<?php

namespace Modules\Vindi\DTO\Customer;

use Spatie\DataTransferObject\DataTransferObject;

class CustomerPhoneDTO extends DataTransferObject
{
    public ?int $id;
    public string $phone_type;
    public string $number;
}