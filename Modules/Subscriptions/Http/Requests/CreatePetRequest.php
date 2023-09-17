<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePetRequest extends FormRequest
{
    public function rules()
    {
        return [
            "nome_pet" => "required",
            "tipo" => "required|in:GATO,CACHORRO",
            "id_raca" => "required|numeric",
            "sexo" => "required|in:M,F,ND",
            "id_cliente" => "required|numeric",
            "data_nascimento" => "required|date_format:d/m/Y"
        ];
    }

}