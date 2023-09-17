<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class SubscriptionPaymentProfileDTO extends DataTransferObject
{
    public string $id;
    public string $token;
    public string $holder_name;
    public string $registry_code;
    public string $bank_branch;
    public string $bank_account;
    public string $card_expiration;
    public string $allow_as_fallback;
    public string $card_number;
    public string $card_cvv;
    public string $card_token;
    public string $gateway_id;
    public string $payment_method_code;
    public string $payment_company_code;
    public string $gateway_token;
}