<?php

use Illuminate\Database\Seeder;

class PrestadoresComoClinicasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clinicasCsv = storage_path('seed/clinicas_e_prestadores.csv');
        $clinicas = \App\Helpers\Utils::csvToArray($clinicasCsv, ";");

        $selection = $clinicas;
        foreach ($selection as &$selected) {
            //$selected["created_at"] = DateTime::createFromFormat('d-m-Y', $selected['created_at']);
            $selected = [
                "id" => $selected['id'],
                "tipo_pessoa" => "PF",
                "cpf_cnpj" => $selected['cpf_cnpj'],
                "nome_clinica" => $selected['nome_clinica'],
                "contato_principal" => '',
                "email_contato" => '',
                "cep" => '',
                "rua" => '',
                "numero_endereco" => '',
                "bairro" => '',
                "cidade" => '',
                "estado" => '',
                "complemento_endereco" => '',
                "telefone_fixo" => '',
                "celular" => '',
                "email_secundario" => '',
                "banco" => '',
                "agencia" => '',
                "numero_conta" => '',
                "crmv" => '0',
                "id_tabela" => '1',
                'tipo' => 'CLINICA'
            ];
            //dd($selected);
            if(empty(\Modules\Clinics\Entities\Clinicas::find($selected['id']))) {
                $clinica = \DB::table('clinicas')->insert($selected);
            }
        }
    }
}
