<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifepetPlusClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_clientes', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('cliente_id')->unsigned();
            $table->string('token');
            $table->enum('meio', ['1', '2', '3'])->comments('Campo que salva por onde o cliente se cadastrou: 1 - Sistema, 2 - App, 3 - Website');
            $table->decimal('taxa_adesao', 10, 2);
            $table->enum('pets_assinados', ['N', 'S']);
            $table->string('ip');
            $table->string('user_agent');

            $table->foreign('cliente_id')->references('id')->on('clientes');

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
        Schema::drop('lifepet_plus_clientes');
    }
}
