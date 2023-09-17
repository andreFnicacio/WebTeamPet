<?php

namespace Modules\Vindi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokenPaymentProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            "holder_name" => 'required',
            "card_expiration" => 'required',
            "card_number" => 'required',
            "card_cvv" => 'required',
            "payment_method_code" => 'required',
            "payment_company_code" => 'required',
            "customer_id" => 'required',
        ];
    }
}