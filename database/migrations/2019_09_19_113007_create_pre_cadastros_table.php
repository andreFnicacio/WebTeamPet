<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreCadastrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_cadastros', function(Blueprint $table) {
            $table->increments('id');
            $table->string('cpf');
            $table->string('nome');
            $table->string('email');
            $table->string('celular');
            $table->string('cidade');
            $table->string('estado');
            $table->date('data_nascimento');
            $table->dateTime('data_adesao')->nullable();
            
            $table->bigInteger('id_cliente')->nullable()->unsigned();
            $table->foreign('id_cliente')->references('id')->on('clientes');
            
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
        Schema::drop('pre_cadastros');
    }
}
