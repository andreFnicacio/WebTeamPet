<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFichasRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichas_respostas', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('id_pergunta')->unsigned();
            $table->integer('id_ficha')->unsigned();
            $table->boolean('resposta');
            $table->string('descricao')->nullable();

            $table->foreign('id_pergunta')->references('id')->on('fichas_perguntas');
            $table->foreign('id_ficha')->references('id')->on('fichas_avaliacoes');

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
        Schema::drop('fichas_respostas');
    }
}
