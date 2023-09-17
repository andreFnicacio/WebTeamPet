<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTabelasProcedimentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabelas_procedimentos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tabela_referencia')->unsigned();
            $table->integer('id_procedimento')->unsigned();
            $table->double('valor')->unsigned();

            $table->foreign('id_tabela_referencia')->references('id')->on('tabelas_referencia');
            $table->foreign('id_procedimento')->references('id')->on('procedimentos');

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
        Schema::drop('tabelas_procedimentos');
    }
}
