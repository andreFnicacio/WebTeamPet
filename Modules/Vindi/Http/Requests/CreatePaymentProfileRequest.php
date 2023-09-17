<?php

namespace Modules\Vindi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            "gateway_token" => 'required',
            "customer_id" => 'required',
            "payment_method_code" => 'required'
        ];
    }
}