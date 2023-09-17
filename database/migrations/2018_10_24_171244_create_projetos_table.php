<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjetosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projetos', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->unsignedInteger('id_departamento');
            $table->unsignedInteger('id_responsavel');

            $table->foreign('id_departamento')->references('id')->on('departamentos');
            $table->foreign('id_responsavel')->references('id')->on('users');

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
        Schema::drop('projetos');
    }
}
