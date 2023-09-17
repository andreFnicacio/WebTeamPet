<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_grupos', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('id_categoria')->unsigned();
            $table->integer('id_grupo')->unsigned();

            $table->foreign('id_categoria')->references('id')->on('categorias');
            $table->foreign('id_grupo')->references('id')->on('grupos_carencias');
            
            $table->unique(['id_grupo', 'id_categoria'], 'grupo_categoria_unique');
            
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
        Schema::drop('categorias_grupos');
    }
}
