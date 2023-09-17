<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class IntegralAdesaoFinalizarAPIRequest extends FormRequest
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
        $return = [
            
            'pet' => 'required',
            // DADOS PESSOAIS
            'nome' => 'required|string',
            'sobrenome' => 'required|string',
            'cpf' => 'required|string|max:14|unique:clientes', // 999.999.999-99
            'rg' => 'nullable|string|max:15',
            'data_nascimento' => 'required|date_format:d/m/Y|before_or_equal:'.Carbon::now()->subYears(18),
            'estado_civil' => 'required|in:SOLTEIRO,CASADO,DIVORCIADO,RELACIONAMENTO ESTAVEL,VIUVO',
            'celular' => 'required|string|max:15', // (27) 99999-9999

            // ENDERECO
            'cep' => 'required|max:10',
            'rua' => 'required|string|max:255',
            'numero_endereco' => 'required|string|max:20',
            'complemento_endereco' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|min:2|max:2', // ES
            
            // DADOS DE PAGAMENTO
            'forma_pagamento' => 'required|in:CARTAO', //,BOLETO
            
            /// CARTAO
            'cartao_nome' => 'required|string',
            'cartao_bandeira' => 'required|in:visa,mastercard,diners,amex,elo',
            'cartao_cod_seguranca' => 'required|numeric',
            'cartao_ano_validade' => 'required|numeric',
            'cartao_mes_validade' => 'required|numeric',
            'cartao_numero' => 'required|numeric',

            // ACESSO
            'email' => 'required|email|unique:users,email|unique:clientes,email',
            'password' => 'required|min:6|confirmed',
            'taxa_adesao' => 'required|in:S,N',
            'ip' => 'required|string'
        ];

        if(isset($this->pet)) {
            foreach($this->request->get('pet') as $k => $pet) {
                // DADOS DO PET (ARRAY)
                $return['pet.'.$k.'.nome'] = 'required|string|max:255';
                $return['pet.'.$k.'.tipo'] = 'required|in:GATO,CACHORRO';
                $return['pet.'.$k.'.id_raca'] = 'required|exists:racas,id|exists:reembolso_racas_precos,id_raca';
                $return['pet.'.$k.'.sexo'] = 'required|in:M,F';
                $return['pet.'.$k.'.data_nascimento'] = 'required|date_format:d/m/Y|after_or_equal:' . Carbon::today()->subYears(40) . '|before_or_equal:'.Carbon::now();
                
                $return['pet.'.$k.'.regime'] = 'sometimes|in:MENSAL,ANUAL';
                $return['pet.'.$k.'.reembolso_porcentagem'] = 'sometimes|in:70,80,90';
                $return['pet.'.$k.'.exame_ultimos_12_meses'] = 'sometimes|boolean';
            }
        }
        
        

        return $return;
    }

    public function messages() {
    
        $return = [
            
            'pet.required' => 'É necessário adicionar ao menos 1 pet',
            // DADOS PESSOAIS
            'nome.required' => 'É necessário preencher seu nome completo',
            'nome.string' => 'Seu nome está inválido',
            'nome.max' => 'Seu nome precisa ter menos que 255 letras',
            'cpf.required' => 'É necessário preencher seu cpf',
            'cpf.string' => 'Seu cpf está inválido',
            'cpf.max' => 'Seu cpf precisa 14 caracteres ou menos',
            'cpf.unique' => 'O CPF preenchido já está em uso',
            'rg.string' => 'Seu RG está inválido',
            'rg.max' => 'Seu RG precisa ter menos que 15 caracteres',
            'data_nascimento.required' => 'É necessário preencher sua data de nascimento',
            'data_nascimento.date' => 'Sua data de nascimento está inválida',
            'data_nascimento.date_format' => 'Sua data de nascimento precisa estar no formato DD/MM/AAAA',
            'data_nascimento.before_or_equal' => 'Você precisa ter 18 anos ou mais. Verifique sua data de nascimento',
            'estado_civil.required' => 'É necessário selecionar seu estado civil',
            'estado_civil.in' => 'Estado civil não encontrado',
            'celular.required' => 'É necessário preencher seu número de telefone celular',
            'celular.string' => 'Seu número de telefone celular está inválido',
            'celular.max' => 'Seu número de telefone celular precisa ter menos que 15 caracteres',

            // ENDEREÇO
            'cep.required' => 'É necessário preencher o campo CEP',
            'cep.max' => 'O CEP preenchido precisa ter menos que 10 caracteres',
    
            'rua.required' => 'É necessário preencher o campo rua',
            'rua.string' => 'A rua preenchida está inválida',
            'rua.max' => 'A rua preenchida precisa ter menos que 255 caracteres',
            'numero_endereco.required' => 'É necessário preencher o campo número',
            'numero_endereco.string' => 'O número preenchida está inválido',
            'numero_endereco.max' => 'O número preenchido precisa ter menos que 10 caracteres',
            'complemento_endereco.string' => 'O complemento preenchido está inválido',
            'complemento_endereco.max' => 'O complemento preenchido precisa ter menos que 255 caracteres',
            'bairro.required' => 'É necessário preencher o campo bairro',
            'bairro.string' => 'O bairro preenchido está inválido',
            'bairro.max' => 'O bairro preenchido precisa ter menos que 255 caracteres',
            'cidade.required' => 'É necessário preencher o campo cidade',
            'cidade.string' => 'A cidade preenchida está inválida',
            'cidade.max' => 'A cidade preenchida precisa ter menos que 255 caracteres',
            'estado.required' => 'É necessário preencher o campo estado',
            'estado.min' => 'O estado preenchido precisa ter no mínimo 2 caracteres',
            'estado.max' => 'O estado preenchido precisa ter no máximo 2 caracteres',
            'cartao_nome.required' => 'O nome do titular do cartão é obrigatório',
            'cartao_nome.string' => 'O nome do titular do cartão está inválido',
            'cartao_bandeira.required' => 'A bandeira do cartão é obrigatório',
            'cartao_bandeira.in' => 'A bandeira selecionada no cartão está inválida',
            'cartao_cod_seguranca.required' => 'O código de segurança do cartão é obrigatório',
            'cartao_cod_seguranca.numeric' => 'O código de segurança do cartão precisa ser apenas números',
            'cartao_ano_validade.required' => 'O ano de validade do cartão é obrigatório',
            'cartao_ano_validade.numeric' => 'O ano de validade do cartão precisa ser apenas números',
            'cartao_mes_validade.required' => 'O mês de validade do cartão é obrigatório',
            'cartao_mes_validade.numeric' => 'O mês de validade do cartão precisa ser apenas números',
            'cartao_numero.required' => 'O número do cartão é obrigatório',
            'cartao_numero.numeric' => 'O número do cartão está inválido. Certifique-se que esteja preenchido apenas números',

            // DADOS DE ACESSO
            'email.required' => 'É necessário preencher o e-mail de acesso',
            'email.email' => 'O e-mail preenchido está inválido',
            'email.unique' => 'O e-mail preenchido já está em uso',
            'password.required' => 'É necessário preencher a senha de acesso',
            'password.min' => 'A senha preenchida precisa ter no mínimo 6 caracteres',
            'password.confirmed' => 'A senha preenchida precisa ser igual à sua confirmação'
        ];

        if(isset($this->pet)) {
            $i = 1;
            foreach($this->request->get('pet') as $k => $pet) {
        
                // DADOS DO PET (ARRAY)
                $return['pet.'.$k.'.nome.required'] = 'É necessário preencher o nome do ' . (count($this->pet) > 1 ? ($i).'º' : '') . 'pet';
                $return['pet.'.$k.'.nome.string'] = 'O nome do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet está inválido';
                $return['pet.'.$k.'.nome.max'] = 'O nome do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet precisa ter menos que 255 caracteres';
                $return['pet.'.$k.'.tipo.required']  = 'É necessário selecionar o tipo do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet';
                $return['pet.'.$k.'.tipo.in'] = 'O tipo do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet selecionado está inválido. Apenas GATO ou CACHORRO';
     
                $return['pet.'.$k.'.id_raca.required'] = 'É necessário selecionar qual é a raça do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet';
                $return['pet.'.$k.'.id_raca.exists'] = 'A raça selecionada do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet não existe em nossa base de dados ou ainda não foi precificada.';
                $return['pet.'.$k.'.sexo.required'] = 'É necessário selecionar qual é o sexo do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet';
                $return['pet.'.$k.'.sexo.in'] = 'O sexo selecionado do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet está inválido. Apenas MACHO ou FÊMEA';
                $return['pet.'.$k.'.data_nascimento.required'] = 'É necessário preencher a data de nascimento do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet';
                $return['pet.'.$k.'.data_nascimento.date'] = 'A data de nascimento do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet está inválida.';
                $return['pet.'.$k.'.data_nascimento.date_format'] = 'A data de nascimento do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet precisa ser no formato DD/MM/AAAA (Ex.: 01/01/2015).';
                $return['pet.'.$k.'.data_nascimento.after_or_equal'] = 'A idade do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet está muito alta. Por favor, verifique a data de nascimento.';
                $return['pet.'.$k.'.data_nascimento.before_or_equal'] = 'A data de nascimento do ' . (count($this->pet) > 1 ? ($i).'º ' : '') . 'pet precisa ser menor ou igual à data atual.';
                $return['pet.'.$k.'.regime.in'] = 'O regime do ' . (count($this->pet) > 1 ? ($i).'º ' : '') .'pet precisa ser MENSAL ou ANUAL';
                $return['pet.'.$k.'.reembolso_porcentagem.in'] = 'A porcentagem do ' . (count($this->pet) > 1 ? ($i).'º ' : '') .'pet precisa ser 70%, 80% ou 90%';
                $return['pet.'.$k.'.exame_ultimos_12_meses.boolean'] = 'É necessário marcar se o ' . (count($this->pet) > 1 ? ($i).'º ' : '') .'pet fez algum exame nos últimos 12 meses';
                $i++;
            }
        }
       

        //dd($return);
        return $return;

    }
}