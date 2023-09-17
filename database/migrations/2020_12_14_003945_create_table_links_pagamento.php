<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLinksPagamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links_pagamento', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('id_cliente')->unsigned();
            $table->float('valor');
            $table->integer('parcelas');
            $table->date('expires_at');
            $table->string('tags');
            $table->string('descricao');
            $table->integer('id_externo')->nullable()->comment('ID que vincula o link a um pagamento no SF');
            $table->string('status')->default('ABERTO');
            $table->string('hash')->comment('Hash único para acesso');


            $table->foreign('id_cliente')->references('id')->on('clientes');

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
        Schema::drop('links_pagamento');
    }
}
