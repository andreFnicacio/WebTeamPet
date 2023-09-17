<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColaboradoresCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colaboradores_cargos', function(Blueprint $table) {
            $table->increments('id');

            $table->date('data_inicio');
            $table->double('salario')->nullable();

            $table->integer('id_colaborador')->unsigned()->nullable();
            $table->foreign('id_colaborador')->references('id')->on('colaboradores');
            $table->integer('id_cargo')->unsigned()->nullable();
            $table->foreign('id_cargo')->references('id')->on('cargos');

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
        Schema::drop('colaboradores');
    }
}
