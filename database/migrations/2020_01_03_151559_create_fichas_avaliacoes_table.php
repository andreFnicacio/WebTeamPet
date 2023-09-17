<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFichasAvaliacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichas_avaliacoes', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('id_pet')->unsigned();
            $table->integer('id_clinica')->unsigned();
            $table->integer('id_prestador')->unsigned()->nullable();
            $table->string('porte');
            $table->string('pelagem');
            $table->string('numero_microchip');
            $table->string('assinatura_cliente')->nullable();
            $table->string('assinatura_prestador')->nullable();

            $table->foreign('id_pet')->references('id')->on('pets');
            $table->foreign('id_clinica')->references('id')->on('clinicas');
            $table->foreign('id_prestador')->references('id')->on('prestadores');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fichas_avaliacoes');
    }
}
