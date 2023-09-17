<?php

use Illuminate\Database\Seeder;

class SelectiveAuthorizeUseHistory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $atividadesCsv = storage_path('csv/atividades_recusadas.csv');
        $atividades = \App\Helpers\Utils::csvToArray($atividadesCsv, ";");

        $selection = $atividades;
        foreach ($selection as &$selected) {
            $found = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', $selected['numero_guia'])
                                             ->where('id_procedimento', $selected['id_procedimento'])
                                             ->first();
            if($found) {
                $found->status = 'RECUSADO';
                $found->update();
                dump($found);
                $found = null;
            }
        }
    }
}
