<?php

use Illuminate\Database\Seeder;

class UpdateIdPetsPlanosPet extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pets = \App\Models\Pets::all();
        $count = 0;

        foreach ($pets as &$pet) {

            if($pet->id_pets_planos === null) {
                $petsPlanos = $pet->petsPlanosAtual()->first();
                if ($petsPlanos) {
                    $pet->id_pets_planos = $petsPlanos->id;
                    $pet->update();
                }
                $count++;
            }

        }

        echo "$count pets tiveram a sua raça atualizada\n";
    }
}
