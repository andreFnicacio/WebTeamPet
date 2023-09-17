<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColaboradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('colaboradores', function(Blueprint $table) {
            $table->increments('id');

            // DADOS PESSOAIS
            $table->string('nome_colaborador');
            $table->enum('tipo_pessoa', ["PF", "PJ"]);
            $table->string('cpf');
            $table->string('cnpj')->nullable();
            $table->string('rg')->nullable();
            $table->string('carteira_trabalho')->nullable();
            $table->date('data_nascimento');
            $table->string('cep')->nullable();
            $table->string('rua');
            $table->string('numero_endereco');
            $table->string('complemento_endereco')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado');
            $table->string('telefone_fixo')->nullable();
            $table->string('celular');
            $table->string('email');
            $table->enum('tipo_sanguineo', ["A+","A−","B+"," B−","AB+","AB−","O+","O−"]);
            $table->text('alergias')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')
                ->default(1)
                ->comment('0 - Inativo, 1 - Ativo');
            $table->enum('sexo', ["M", "F", "O"]);
            $table->enum('estado_civil', [
                "SOLTEIRO",
                "CASADO",
                "DIVORCIADO",
                "RELACIONAMENTO ESTAVEL",
                "VIUVO"
            ]);

            // CONTATOS DE EMERGENCIA
            $table->string('emergencia_nome')->nullable();
            $table->string('emergencia_telefone')->nullable();
            $table->string('emergencia_email')->nullable();

            // DADOS CONTRATUAIS
            $table->string('numero_contrato');
            $table->date('data_inicio_contrato');
            $table->date('data_encerramento_contrato')->nullable();
            $table->double('salario')->nullable();

            // FKs
            $table->integer('id_usuario')->unsigned()->nullable();
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->integer('id_cargo')->unsigned()->nullable();
            $table->foreign('id_cargo')->references('id')->on('cargos');

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
        Schema::drop('colaboradores');
    }
}
