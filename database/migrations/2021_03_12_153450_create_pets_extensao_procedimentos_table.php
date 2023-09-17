<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetsExtensaoProcedimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets_extensao_procedimentos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pet')->unsigned();
            $table->integer('id_procedimento')->unsigned();
            $table->timestamps();

            $table->foreign('id_pet')->references('id')->on('pets');
            $table->foreign('id_procedimento')->references('id')->on('procedimentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pets_extensao_table');
    }
}
