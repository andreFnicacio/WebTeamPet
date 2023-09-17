<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('pets', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome_pet');
            $table->enum('tipo', ["GATO", "CACHORRO"]);
//            $table->string('raca');
            $table->string('id_externo')->nullable();
            $table->string('numero_microchip');
            $table->date('data_nascimento');

            $table->bigInteger('id_cliente')->unsigned();

            $table->boolean('contem_doenca_pre_existente')->default(0);
            $table->text('doencas_pre_existentes')->nullable();
            //Movidos para 'pets_planos'
//            $table->date('data_contrato');
//            $table->date('data_encerramento')->nullable();
            $table->boolean('familiar')->default(0);
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')
                  ->default(1)
                  ->comment('0 - Inativo, 1 - Ativo');
            $table->enum('sexo', ["M", "F", "ND"])->default("ND");

            

            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('pets');
    }
}
