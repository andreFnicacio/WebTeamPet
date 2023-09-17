<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreinamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treinamentos', function(Blueprint $table) {
            $table->increments('id')->comment('Sequência única de criação do treinamento');
            $table->integer('autor')->unsigned();
            $table->dateTime('published_at');
            $table->timestamps();
            $table->string('titulo');
            $table->text('corpo');
            $table->double('duracao');
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('treinamentos');
    }
}
