<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultoresDadosGeraisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:pending,pending_data,active,blocked,canceled',
            'status_reason' => 'nullable|string',
            'status_reason_send_email' => 'in:1,0',
            'status_reason_pendency' => 'in:1,0',
            'name' => 'required|string',
            'email' => 'required|email',
            'rg' => 'required|string',
            'cpf' => 'required|string',
            'phone' => 'required|string',
            'phone2' => 'nullable|string',
            'waiting_days' => 'required|integer'
        ];
    }
}
