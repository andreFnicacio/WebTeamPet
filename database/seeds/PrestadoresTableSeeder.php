<?php

use Illuminate\Database\Seeder;

class PrestadoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prestadoresCsv = storage_path('seed/prestadores.csv');
        $prestadores = \App\Helpers\Utils::csvToArray($prestadoresCsv, ";");

        $selection = $prestadores;
        foreach ($selection as &$selected) {
            $selected["created_at"] = DateTime::createFromFormat('d-m-Y', $selected['created_at']);
            $selected = [
                "id" => $selected['id'],
                "nome" => $selected['nome'],
                "cpf" => $selected['cpf'],
                "crmv" => $selected['crmv'],
                "email" => $selected['email'] ?: "",
                "especialista" => $selected['especialista'],
                "id_especialidade" => $selected['id_especialidade'] ?: null,
                "telefone" => $selected['telefone'] ?: "",
                "tipo_pessoa" => $selected['tipo_pessoa'],
                "created_at" => $selected['created_at'] ?: null,
            ];


            $prestador = \DB::table('prestadores')->insert($selected);
        }
    }
}
