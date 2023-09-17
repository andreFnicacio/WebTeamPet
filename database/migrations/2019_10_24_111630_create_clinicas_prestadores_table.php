<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicasPrestadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinicas_prestadores', function($table) {
            $table->increments('id');

            $table->integer('id_prestador')->unsigned();
            $table->integer('id_clinica')->unsigned();

            $table->foreign('id_prestador')->references('id')->on('prestadores');
            $table->foreign('id_clinica')->references('id')->on('clinicas');
            
            $table->unique(['id_clinica', 'id_prestador'], 'clinica_prestador_unique');
            
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
        Schema::drop('clinicas_prestadores');
    }
}
