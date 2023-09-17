<?php

namespace Modules\Vindi\DTO\PaymentProfile;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentProfileDTO extends DataTransferObject
{
    public int $id;
    public string $status;
    public string $holder_name;
    public ?string $registry_code;
    public ?string $bank_branch;
    public ?string $bank_account;
    public ?string $card_expiration;
    public ?bool $allow_as_fallback;
    public ?string $card_number_first_six;
    public ?string $card_number_last_four;
    public ?PaymentProfileRenewedCardDTO $renewed_card;
    public ?string $card_renewed_at;
    public ?string $token;
    public ?string $gateway_token;
    public string $type;
    public string $created_at;
    public string $updated_at;
    public ?PaymentProfilePaymentCompanyDTO $payment_company;
    public ?PaymentProfilePaymentMethodDTO $payment_method;
    public ?PaymentProfileCustomerDTO $customer;
}