<?php

use Illuminate\Database\Seeder;

class ClinicasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clinicasCsv = storage_path('seed/clinicas.csv');
        $clinicas = \App\Helpers\Utils::csvToArray($clinicasCsv, ";");

        $selection = $clinicas;
        foreach ($selection as &$selected) {
            //$selected["created_at"] = DateTime::createFromFormat('d-m-Y', $selected['created_at']);
            $selected = [
                "id" => $selected['id'],
                "tipo_pessoa" => $selected['tipo_pessoa'],
                "cpf_cnpj" => $selected['cpf_cnpj'],
                "nome_clinica" => $selected['nome_clinica'],
                "contato_principal" => $selected['contato_principal'],
                "email_contato" => $selected['email_contato'] ?: "",
                "cep" => $selected['cep'] ?: "",
                "rua" => $selected['rua'] ?: "",
                "numero_endereco" => $selected['numero_endereco'] ?: "",
                "bairro" => $selected['bairro'] ?: "",
                "cidade" => $selected['cidade'] ?: "",
                "estado" => $selected['estado'] ?: "",
                "complemento_endereco" => $selected['complemento_endereco'] ?: "",
                "telefone_fixo" => $selected['telefone_fixo'] ?: "",
                "celular" => $selected['celular'] ?: "",
                "email_secundario" => $selected['email_secundario'] ?: "",
                "banco" => $selected['banco'] ?: "",
                "agencia" => $selected['agencia'] ?: "",
                "numero_conta" => $selected['numero_conta'] ?: "",
                "crmv" => $selected['crmv'],
                "id_tabela" => '1',
                'tipo' => 'CLINICA'
            ];
            //dd($selected);
            $clinica = \DB::table('clinicas')->insert($selected);
        }
    }
}
