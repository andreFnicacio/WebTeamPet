<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvaliacoesPrestadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacoes_prestadores', function($table) {
            $table->increments('id');

            $table->bigInteger('id_cliente')->unsigned();
            $table->integer('id_prestador')->unsigned();
            $table->integer('numero_guia')->unsigned()->nullable();
            $table->integer('id_clinica')->unsigned();
            $table->integer('id_pet')->unsigned();
            $table->enum('nota', [1, 2, 3, 4, 5])->nullable();
            $table->string('comentario')->nullable();
            $table->boolean('publico')->default(0);

            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_prestador')->references('id')->on('prestadores');
            $table->foreign('id_clinica')->references('id')->on('clinicas');
            $table->foreign('id_pet')->references('id')->on('pets');

            // $table->unique(['numero_guia', 'id_prestador', 'id_cliente'], 'avaliacao_guia_prestador_cliente_unique');

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
        Schema::drop('avaliacoes_prestadores');
    }
}
