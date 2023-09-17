<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifepetPlusSuperlogicaAssinaturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_superlogica_assinaturas', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('lifepet_plus_assinatura_id')->unsigned();
            $table->integer('superlogica_id')->unsigned();
            $table->integer('id_primeira_cobranca')->nullable();
            $table->enum('status_primeira_cobranca', ['PENDENTE', 'CONCLUIDO']);
            $table->string('data_alteracao_primeira_cobranca')->nullable()->comments('Campo em string por ser um valor externo e não assegurado');
            $table->datetime('data_pagamento_primeira_cobranca')->nullable();
            $table->datetime('data_ultima_verificacao')->nullable();
            $table->integer('qtde_verificacoes');
            $table->foreign('lifepet_plus_assinatura_id', 'lpa_id_foreign')->references('id')->on('lifepet_plus_assinaturas');
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
        Schema::drop('lifepet_plus_superlogica_assinaturas');
    }
}
