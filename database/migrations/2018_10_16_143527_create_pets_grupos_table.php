<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetsGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets_grupos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('dias_carencia')->comment('Sobrescreve os dias de carência padrão de PLANOS_GRUPOS');
            $table->integer('quantidade_usos');
            $table->boolean('liberacao_automatica')->default(false);

            $table->integer('id_pet')->unsigned();
            $table->foreign('id_pet')->references('id')->on('pets');

            $table->integer('id_grupo')->unsigned();
            $table->foreign('id_grupo')->references('id')->on('grupos_carencias');

            $table->unique(['id_pet', 'id_grupo']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pets_grupos');
    }
}
