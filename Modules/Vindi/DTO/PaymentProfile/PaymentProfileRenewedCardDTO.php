<?php

namespace Modules\Vindi\DTO\PaymentProfile;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentProfileRenewedCardDTO extends DataTransferObject
{
    public ?string $card_number_last_four;
    public ?string $card_expiration;
}