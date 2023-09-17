<?php

use Illuminate\Database\Seeder;

class PlanosCredenciadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planos = \App\Models\Planos::all();
        $credenciados = \Modules\Clinics\Entities\Clinicas::all();

        foreach($planos as $p) {

            foreach($credenciados as $c) {
                \App\Models\PlanosCredenciados::create([
                    'id_plano' => $p->id,
                    'id_clinica' => $c->id,
                    'habilitado' => 1
                ]);
            }
            echo "Adicionado: Plano ({$p->nome_plano})\n";
        }
    }
}
