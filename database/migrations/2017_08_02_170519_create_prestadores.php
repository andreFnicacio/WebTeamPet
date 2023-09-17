<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrestadores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestadores', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_clinica')->unsigned()->nullable();
            $table->enum('tipo_pessoa', ["PF", "PJ"]);
            $table->string('cpf')->nullable();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone');
            $table->string('crmv');
            $table->boolean('especialista')->default(0);
            $table->integer('id_especialidade')->unsigned()->nullable();

            $table->foreign('id_clinica')->references('id')->on('clinicas');
            $table->foreign('id_especialidade')->references('id')->on('especialidades');
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
        Schema::drop('prestadores');
    }
}
