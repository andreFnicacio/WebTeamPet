<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome')->unique();
            $table->double('salario_base')->default(0);
            $table->string('sindicato')->nullable();
            $table->text('descricao')->nullable();

            $table->unsignedInteger('id_departamento');
            $table->foreign('id_departamento')->references('id')->on('departamentos');

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
        Schema::drop('cargos');
    }
}
