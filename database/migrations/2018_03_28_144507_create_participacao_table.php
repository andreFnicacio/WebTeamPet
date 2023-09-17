<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participacao', function(Blueprint $table) {
           $table->increments('id');
           $table->integer('id_historico_uso');
           $table->bigInteger('id_cliente')->unsigned();
           $table->integer('id_pet')->unsigned();
           $table->double('valor_participacao');
           $table->date('vigencia_inicio');
           $table->date('vigencia_fim');
           $table->string('competencia')->comment('Identifica o mês e o ano em que essa participação será cobrada.');
           $table->integer('id_externo')->comment('Identificador externo da cobrança gerada.')->nullable();
           $table->timestamps();

           $table->foreign('id_pet')->references('id')->on('pets');
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
        Schema::drop('participacao');
    }
}
