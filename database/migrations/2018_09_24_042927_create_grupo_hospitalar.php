<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrupoHospitalar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_hospitalares', function(Blueprint $table) {
            $table->increments('id');
            $table->enum('tipo_pessoa', ["PF", "PJ"]);
            $table->string('cpf_cnpj', 30);
            $table->string('nome_grupo');
            $table->string('contato_principal');
            $table->string('email_contato');
            $table->string('cep');
            $table->string('rua');
            $table->string('numero_endereco');
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado');
            $table->string('complemento_endereco')->nullable();
            $table->string('telefone_fixo')->nullable();
            $table->string('celular');
            $table->string('email_secundario');
            $table->string('banco');
            $table->string('agencia');
            $table->string('numero_conta');

            $table->integer('id_usuario')->nullable()->unsigned();
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
        Schema::drop('grupos_hospitalares');
    }
}
