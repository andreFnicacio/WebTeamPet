<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanosCredenciados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planos_credenciados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_plano')->unsigned();
            $table->integer('id_clinica')->unsigned();
            $table->boolean('habilitado')->default(1)->comment('1: Habilitado, 0: Não habilitado');
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('id_plano')->references('id')->on('planos');
            $table->foreign('id_clinica')->references('id')->on('clinicas');

            $table->unique(['id_plano', 'id_clinica']);

            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('planos_credenciados');
    }
}
