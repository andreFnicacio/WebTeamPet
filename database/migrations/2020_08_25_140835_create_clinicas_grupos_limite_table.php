<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicasGruposLimiteTable extends Migration
{
    /**
     * Run the migrations.
     * Cria a tabela de limite de usos por clínica.
     * @return void
     */
    public function up()
    {
        Schema::create('clinicas_grupos_limites', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_clinica')->unsigned();
            $table->integer('id_grupo')->unsigned();
            $table->bigInteger('limite')->default('0');

            $table->foreign('id_clinica')->references('id')->on('clinicas');
            $table->foreign('id_grupo')->references('id')->on('grupos_carencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clinicas_grupos_limites');
    }
}
