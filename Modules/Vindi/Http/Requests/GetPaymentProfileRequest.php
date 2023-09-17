<?php

namespace Modules\Vindi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPaymentProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer_id' => 'required|numeric'
        ];
    }
}