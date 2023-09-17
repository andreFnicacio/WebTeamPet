<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSugestoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sugestoes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_usuario')->unsigned();
            $table->string('titulo');
            $table->text('corpo');
            $table->boolean('lido')->default(0);
            $table->integer('visto_por')->unsigned()->nullable();
            $table->boolean('realizado')->default(0);
            $table->integer('realizador')->unsigned()->nullable();
            $table->integer('prioridade')->default(1);
            $table->boolean('arquivada')->default(0);
            $table->nullableTimestamps();

            $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sugestoes');
    }
}
