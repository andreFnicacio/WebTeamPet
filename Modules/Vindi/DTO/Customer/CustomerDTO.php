<?php

namespace Modules\Vindi\DTO\Customer;

use Spatie\DataTransferObject\DataTransferObject;

class CustomerDTO extends DataTransferObject
{
    public int $id;
    public string $name;
    public ?string $email;
    public ?string $registry_code;
    public string $code;
    public string $status;
    public string $created_at;
    public string $updated_at;
    public ?string $notes;
    public ?array $metadata;
    public ?CustomerAddressDTO $address;
    public ?CustomerPhoneDTOCollection $phones;
}