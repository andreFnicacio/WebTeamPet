<?php

use Illuminate\Database\Seeder;

class UpdateRacaPets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pets = \App\Models\Pets::all();
        $racas = \App\Models\Raca::all();
        $count = 0;

        foreach ($pets as &$pet) {
            if($pet->raca === 'Não Informado') {
                $pet->raca === "SRD *";
                $pet->id_raca = 1;
                $pet->update();
                $count++;
            } else {
                $racasFiltradas = $racas->filter(function($raca) use ($pet) {
                    if(strpos(strtoupper($raca->nome), strtoupper($pet->raca)) !== false &&
                        $pet->tipo === $raca->tipo) {
                        return $raca;
                    }
                });

                if($racasFiltradas->isNotEmpty()) {
                    $raca = $racasFiltradas->first();
                    $pet->raca = $pet->raca . " *";
                    $pet->id_raca = $raca->id;
                    $pet->update();
                    $count++;
                }
            }
        }

        echo "$count pets tiveram a sua raça atualizada\n";
    }

}
