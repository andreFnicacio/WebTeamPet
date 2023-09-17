<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePetPlanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id_pet' => 'required|numeric',
            'id_plano' => 'required|numeric',
            'valor_momento' => 'required|numeric',
            'data_inicio_contrato' => 'required|date_format:d/m/Y',
            'regime' => 'required|in:MENSAL,ANUAL'
        ];
    }
}