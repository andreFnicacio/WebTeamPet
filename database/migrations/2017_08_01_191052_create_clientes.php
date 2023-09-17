<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('clientes')) {
            return;
        }

        Schema::create('clientes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome_cliente');
            $table->string('cpf');
            $table->string('rg')->nullable();
            $table->date('data_nascimento');
            $table->string('numero_contrato');
            $table->string('cep');
            $table->string('rua');
            $table->string('numero_endereco');
            $table->string('complemento_endereco')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado');
            $table->string('telefone_fixo')->nullable();
            $table->string('celular');
            $table->string('email')->nullable();
            $table->boolean('ativo')
                  ->default(1)
                  ->comment('0 - Inativo, 1 - Ativo');                  
            $table->integer('id_externo')->unsigned()->nullable();
            $table->enum('sexo', ["M", "F", "O"]);
            $table->enum('estado_civil', [
                "SOLTEIRO", 
                "CASADO", 
                "DIVORCIADO", 
                "RELACIONAMENTO ESTAVEL",
                "VIUVO"
            ]);

            $table->text('observacoes')->nullable();

            $table->boolean('participativo')->default(0);
            $table->integer('id_conveniado')->unsigned()->nullable();
            $table->integer('id_usuario')->unsigned()->nullable();
            $table->integer('vencimento')->default(10);
            $table->double('valor')->default(0);

            $table->foreign('id_conveniado')->references('id')->on('conveniados');
            $table->foreign('id_usuario')->references('id')->on('users');

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
        Schema::drop('clientes');
    }
}
