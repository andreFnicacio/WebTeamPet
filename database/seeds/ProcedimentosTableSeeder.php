<?php

use Illuminate\Database\Seeder;

class ProcedimentosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedimentosCsv = storage_path('seed/procedimentos.csv');
        $procedimentos = \App\Helpers\Utils::csvToArray($procedimentosCsv, ";");

        $selection = $procedimentos;
        foreach ($selection as &$selected) {
            $selected = [
                "id" => $selected['cod_procedimento'],
                "cod_procedimento" => $selected['cod_procedimento'],
                "nome_procedimento" => $selected['nome_procedimento'],
                "especialista" => $selected['especialista'],
                "intervalo_usos" => $selected['intervalo_usos'] ?: "",
                "valor_base" => $selected['valor_base'],
                "id_grupo" => $selected['id_grupo']
            ];

            $prestador = \DB::table('procedimentos')->insert($selected);
        }
    }
}
