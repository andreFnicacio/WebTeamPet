<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ferias', function (Blueprint $table) {
            $table->increments('id');

            $table->date('data_inicio');
            $table->date('data_encerramento');

            $table->integer('id_colaborador')->unsigned();
            $table->foreign('id_colaborador')->references('id')->on('colaboradores');

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
        Schema::dropIfExists('ferias');
    }
}
