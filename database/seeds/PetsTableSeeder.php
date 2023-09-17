<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class PetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $petsCsv = storage_path('seed/pets-2.csv');
        $pets = \App\Helpers\Utils::csvToArray($petsCsv, ";");

        $selection = $pets;
        foreach ($selection as &$selected) {
            if(\App\Models\Pets::find($selected['id'])) {
                continue;
            }

            if(!isset($selected['raca'])) {
                $selected['raca'] = "Não Informado";
            }
            if(!isset($selected['id_externo']) || !$selected['id_externo']) {
                $selected['id_externo'] = 0;
            }
            if(!isset($selected['data_encerramento']) || !$selected['data_encerramento']) {
                $selected['data_encerramento'] = null;
            }
            $insert = [
                'id' => $selected['id'],
                'nome_pet' => $selected['nome_pet'],
                'observacoes' => $selected['observacoes'],
                'data_nascimento' => \Carbon\Carbon::createFromFormat("Y-m-d", $selected['data_nascimento']),
                'tipo' => $selected['tipo'],
                'ativo' => $selected['ativo'],
                'id_plano' => $selected['id_plano'],
                'numero_microchip' => $selected['numero_microchip'],
                'created_at' => \Carbon\Carbon::createFromFormat("Y-m-d", $selected['created_at']),
                'id_cliente' => $selected['id_cliente'],
                'contem_doenca_pre_existente' => $selected['contem_doenca_pre_existente'],
                'doencas_pre_existentes' => $selected['doencas_pre_existentes'],
                'familiar' => $selected['familiar'],
                'raca' => $selected['raca'],
                'id_externo' => $selected['id_externo'],
            ];

            $pet = (object) \DB::table('pets')->insert($insert);
            $plano = \App\Models\Planos::find($selected['id_plano']);
            //Assign plan to Pet

            $petsPlanos = \DB::table('pets_planos')->insert([
                'id_pet' => $selected['id'],
                'id_plano' => $plano->id,
                'valor_momento' => $plano->preco_plano_individual,
                'data_inicio_contrato' => \Carbon\Carbon::createFromFormat("Y-m-d", $selected['data_contrato']),
                'data_encerramento_contrato' => $selected['data_encerramento'],
            ]);
        }
    }
}
