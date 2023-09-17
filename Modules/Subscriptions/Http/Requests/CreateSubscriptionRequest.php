<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer' => 'required',
            'customer.nome_cliente' => 'required',
            'customer.email' => 'required|email',
            'customer.cpf' => 'required|cpf',
            'customer.data_nascimento' => 'required|date_format:d/m/Y',
            'customer.sexo' => 'required|in:M,F,O',
            'customer.celular' => 'required|celular_com_ddd',
            'customer.rua' => 'required',
            'customer.numero_endereco' => 'numeric',
            'customer.bairro' => 'required',
            'customer.cidade' => 'required',
            'customer.estado' => 'required',
            'customer.cep' => 'required',

            'payment' => 'required',
            'payment.payment_method_code' => 'required',
            'payment.installments' => 'required',

            'pets.*.nome_pet' => 'required',
            'pets.*.tipo' => 'required|in:GATO,CACHORRO',
            'pets.*.id_raca' => 'required|numeric',
            'pets.*.sexo' => 'required|in:M,F,ND',

            'pets.*.subscription.id_plano' => 'required|numeric',
            'pets.*.subscription.valor_momento' => 'required|numeric',
            'pets.*.subscription.data_inicio_contrato' => 'required|date_format:d/m/Y',
            'pets.*.subscription.regime' => 'required|in:MENSAL,ANUAL'
        ];
    }
}