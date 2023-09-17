<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGruposClinicas extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_clinicas', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_grupo_hospitalar')->nullable()->unsigned();
            $table->integer('id_clinica')->nullable()->unsigned();
            $table->foreign('id_grupo_hospitalar')->references('id')->on('grupos_hospitalares');
            $table->foreign('id_clinica')->references('id')->on('clinicas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::drop('grupos_clinicas');
    }
}
