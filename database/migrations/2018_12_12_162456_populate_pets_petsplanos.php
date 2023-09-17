<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulatePetsPetsplanos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pets = \App\Models\Pets::all();
        foreach ($pets as $pet) {
            dump("Atualizando 'id_pets_planos' do Pet {$pet->id}...\n");
            $pp = $pet->petsPlanos()->orderBy('id', 'DESC')->first();
            if($pp) {
                $pet->id_pets_planos = $pp->id;
                $pet->update();
            } else {
                dump("Pet {$pet->id} sem plano vinculado!\n");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
