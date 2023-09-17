<?php

use Illuminate\Database\Seeder;

class CreateRacasGatos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $racasGatosTxt = storage_path('csv/racas_gatos.txt');
        $racasGatos = explode("\n", file_get_contents($racasGatosTxt));



        $selection = array_map(function($raca) {
            return [
                'slug' => \App\Helpers\Utils::slugify($raca),
                'nome' => $raca,
                'tipo' => 'GATO'
            ];
        }, $racasGatos);

        foreach ($selection as &$selected) {
            if(!\DB::table('racas')->where('nome', $selected['nome'])->exists()) {
                $raca = \DB::table('racas')->insert($selected);
                dump($raca);
            } else {
                echo "Raça {$selected['nome']} já foi cadastrada anteriormente.\n";
            }
        }
    }
}
