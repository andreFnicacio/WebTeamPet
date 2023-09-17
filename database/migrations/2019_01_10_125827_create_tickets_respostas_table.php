<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_respostas', function(Blueprint $table) {
            $table->increments('id');
            $table->text('descricao');

            $table->unsignedInteger('id_colaborador');
            $table->foreign('id_colaborador')->references('id')->on('colaboradores');

            $table->unsignedInteger('id_ticket');
            $table->foreign('id_ticket')->references('id')->on('tickets');

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
        Schema::drop('tickets_respostas');
    }
}
