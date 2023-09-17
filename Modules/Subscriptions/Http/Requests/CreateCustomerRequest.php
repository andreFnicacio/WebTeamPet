<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function rules()
    {
        return [
            "nome_cliente" => "required",
            "email" => "required|email",
            "cpf" => "required",
            "data_nascimento" => "required|date_format:d/m/Y",
            "sexo" => "required|in:M,F,O",
            "celular" => "required|numeric",
            "rua" => "required",
            "numero_endereco" => "numeric",
            "bairro" => "required",
            "cidade" => "required",
            "estado" => "required",
            "cep" => "required",
            "ativo" => "required|boolean"
        ];
    }
}