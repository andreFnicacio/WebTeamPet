<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPetsOptionsJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets_opcoes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pet')->unsigned();
            $table->string('chave');
            $table->string('valor');

            $table->unique(['id_pet', 'chave']);
            $table->foreign('id_pet')->references('id')->on('pets');

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
        Schema::drop('pets_opcoes');
    }
}
