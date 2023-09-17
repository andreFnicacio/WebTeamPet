<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreinamentosDepartamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treinamentos_departamentos', function(Blueprint $table) {
            $table->integer('id_treinamento')->unsigned();
            $table->integer('id_departamento')->unsigned();

            $table->unique(['id_treinamento', 'id_departamento']);

            $table->foreign('id_treinamento')->references('id')->on('treinamentos');
            $table->foreign('id_departamento')->references('id')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('treinamentos_departamentos');
    }
}
