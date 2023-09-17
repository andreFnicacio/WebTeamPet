<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheets', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tarefa');
            $table->unsignedInteger('id_usuario');
            $table->dateTime('inicio');
            $table->dateTime('fim')->nullable();

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_tarefa')->references('id')->on('tarefas');

            $table->bigInteger('duracao')->nullable();

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
        Schema::drop('timesheets');
    }
}
