<?php

use Illuminate\Database\Seeder;

class HistoricoUsosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $historico_usoCsv = storage_path('csv/ultimo_historico.csv');
        $historico_uso    = \App\Helpers\Utils::csvToArray($historico_usoCsv, ",");
        //$historico_uso    = array_slice($historico_uso, 0, 10);

        $selection = $historico_uso;
        $errors = [];
        foreach ($selection as &$selected) {
            //unset($selected['id']);

            $selected['created_at'] = \DateTime::createFromFormat('Y-m-d', $selected['created_at']);
            if(isset($selected['updated_at'])) {
                $selected['updated_at'] = \DateTime::createFromFormat('Y-m-d', $selected['created_at']);
            }
            unset($selected['autorizacao']);
            unset($selected['tipo_atendimento']);
            //unset($selected['data']);
            if($selected['id_prestador'] = "NULL") {
                $selected['id_prestador'] = null;
            }
            if($selected['status'] != 'LIBERADO' || $selected['status'] != 'RECUSADO') {
                $selected['status'] = 'LIBERADO';
            }
            if(empty($selected['updated_at'])) {
                $selected['updated_at'] = null;
            }
            //dd($selected);
            if(!\Modules\Guides\Entities\HistoricoUso::where('numero_guia', $selected['numero_guia'])
                                        //->where('id_procedimento', $selected['id_procedimento'])
                                        ->first()) {
                $historico = \DB::table('historico_uso')->insert($selected);
            } else {
                $errors[] = $selected['numero_guia'];
                dump("Guia de número " . $selected['numero_guia'] . " já existe no sistema. Busque uma solução.");
            }
            dump($selected);
        }
        dump($errors);
    }
}
