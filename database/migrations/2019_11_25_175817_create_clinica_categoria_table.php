<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicaCategoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinicas_categorias', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('id_categoria')->unsigned();
            $table->integer('id_clinica')->unsigned();

            $table->foreign('id_categoria')->references('id')->on('categorias');
            $table->foreign('id_clinica')->references('id')->on('clinicas');
            
            $table->unique(['id_clinica', 'id_categoria'], 'clinica_categoria_unique');
            
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
        Schema::drop('clinicas_categorias');
    }
}
