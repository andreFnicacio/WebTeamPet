<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformacoesAdicionaisVinculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informacoes_adicionais_vinculos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_informacoes_adicionais')->unsigned();
            $table->string('tabela_vinculada');
            $table->integer('id_vinculado');
            $table->timestamps();

            $table->foreign('id_informacoes_adicionais', 'add_info')->references('id')->on('informacoes_adicionais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('informacoes_adicionais_vinculos');
    }
}
