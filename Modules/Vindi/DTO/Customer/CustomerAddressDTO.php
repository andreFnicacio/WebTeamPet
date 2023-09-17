<?php

namespace Modules\Vindi\DTO\Customer;

use Spatie\DataTransferObject\DataTransferObject;

class CustomerAddressDTO extends DataTransferObject
{
    public string $street;
    public ?string $number;
    public ?string $additional_details;
    public string $zipcode;
    public string $neighborhood;
    public string $city;
    public string $state;
    public string $country;
}