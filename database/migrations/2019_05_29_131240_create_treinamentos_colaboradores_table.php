<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreinamentosColaboradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treinamentos_colaboradores', function(Blueprint $table) {
            $table->integer('id_treinamento')->unsigned();
            $table->integer('id_colaborador')->unsigned();

            $table->unique(['id_treinamento', 'id_colaborador']);

            $table->foreign('id_treinamento')->references('id')->on('treinamentos');
            $table->foreign('id_colaborador')->references('id')->on('colaboradores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('treinamentos_colaboradores');
    }
}
