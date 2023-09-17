<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOcorrenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ocorrencias', function(Blueprint $table) {
            $table->increments('id');

            $table->enum('tipo', ['Ponto Eletrônico', 'Outro']);
            $table->string('assunto');
            $table->string('descricao');
            $table->dateTime('data_ocorrencia');

            $table->integer('id_colaborador')->unsigned();
            $table->foreign('id_colaborador')->references('id')->on('colaboradores');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ocorrencias');
    }
}
