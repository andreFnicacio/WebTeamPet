<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePlanosConfiguracoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planos_configuracoes', function(Blueprint $table) {
            $table->integer('id_plano')->unsigned();

            $table->string('rd__gatilho_lead')->nullable();
            $table->string('rd__gatilho_pagamento_confirmado')->nullable();
            $table->string('rd__gatilho_erro_pagamento')->nullable();
            $table->string('rd__gatilho_boas_vindas')->nullable();

            $table->foreign('id_plano')->references('id')->on('planos');
            $table->unique('id_plano');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('planos_configuracoes');
    }
}
