<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlteracoesCadastraisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alteracoes_cadastrais', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_usuario')->unsigned();
            $table->bigInteger('id_cliente')->unsigned();
            $table->text('corpo');
            $table->string('telefone');
            $table->boolean('lido')->default(0);
            $table->integer('visto_por')->unsigned()->nullable();
            $table->boolean('realizado')->default(0);
            $table->integer('realizador')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('alteracoes_cadastrais');
    }
}
