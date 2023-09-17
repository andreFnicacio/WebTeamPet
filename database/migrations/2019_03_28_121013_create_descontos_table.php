<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescontosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descontos', function(Blueprint $table) {
            $table->increments('id');

            $table->decimal('desconto_aplicado', 5, 2);
            $table->enum('regra_desconto', ['simples', 'composto']);

            $table->integer('id_guia')->unsigned();
            $table->foreign('id_guia')->references('id')->on('historico_uso');

            $table->integer('id_guia_secundaria')->unsigned();
            $table->foreign('id_guia_secundaria')->references('id')->on('historico_uso');

            $table->integer('id_desconto_tipo')->unsigned();
            $table->foreign('id_desconto_tipo')->references('id')->on('descontos_tipos');

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
        Schema::drop('descontos');
    }
}
