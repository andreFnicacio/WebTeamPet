<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtasColaboradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atas_colaboradores', function(Blueprint $table) {
            $table->integer('id_ata')->unsigned();
            $table->integer('id_colaborador')->unsigned();

            $table->unique(['id_ata', 'id_colaborador']);

            $table->foreign('id_ata')->references('id')->on('atas');
            $table->foreign('id_colaborador')->references('id')->on('colaboradores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('atas_colaboradores');
    }
}
