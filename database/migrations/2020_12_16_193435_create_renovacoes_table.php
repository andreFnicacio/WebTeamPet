<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRenovacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renovacoes', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('id_cliente')->unsigned();
            $table->integer('id_pet')->unsigned();
            $table->integer('id_plano')->unsigned();
            $table->string('status');
            $table->integer('id_link_pagamento')->unsigned()->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('regime');
            $table->float('valor');
            $table->float('valor_original');
            $table->float('desconto')->default(0);

            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_pet')->references('id')->on('pets');
            $table->foreign('id_plano')->references('id')->on('planos');
            $table->foreign('id_link_pagamento')->references('id')->on('links_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('renovacoes');
    }
}
