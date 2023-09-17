<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendedoresPontuacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendedores_pontuacao', function($table) {
            $table->increments('id');
            $table->integer('id_vendedor')->unsigned();
            $table->integer('id_venda')->unsigned();
            $table->integer('pontuacao')->unsigned();

            $table->foreign('id_vendedor')->references('id')->on('vendedores');
            $table->foreign('id_venda')->references('id')->on('vendas');

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
        Schema::drop('vendedores_pontuacao');
    }
}
