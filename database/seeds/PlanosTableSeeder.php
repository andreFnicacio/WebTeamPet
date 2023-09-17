<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class PlanosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planosCsv = storage_path('seed/planos.csv');
        $planos = \App\Helpers\Utils::csvToArray($planosCsv, ";");

        $selection = $planos;
        foreach ($selection as &$selected) {
            $selected["data_inatividade"] = DateTime::createFromFormat('d/m/Y', $selected['data_inatividade']);
            $selected["data_vigencia"] = DateTime::createFromFormat('d/m/Y', $selected['data_vigencia']);
            if(!$selected['data_inatividade']) {
                $selected['data_inatividade'] = null;
            }
            //dd($selected);

            $plano = \DB::table('planos')->insert($selected);
        }
    }
}
