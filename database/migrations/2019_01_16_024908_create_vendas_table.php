<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function($table) {
            $table->increments('id');
            $table->bigInteger('id_cliente')->unsigned();
            $table->integer('id_vendedor')->unsigned();
            $table->integer('id_pet')->unsigned();
            $table->integer('id_plano')->unsigned();
            $table->float('adesao')->default(0);
            $table->float('valor');
            $table->datetime('data_inicio_contrato');

            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_vendedor')->references('id')->on('vendedores');
            $table->foreign('id_pet')->references('id')->on('pets');
            $table->foreign('id_plano')->references('id')->on('planos');

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
        Schema::drop('vendas');
    }
}
