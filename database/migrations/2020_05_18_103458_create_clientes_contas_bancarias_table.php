<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesContasBancariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_contas_bancarias', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome_completo');
            $table->enum('tipo_pessoa', ['Física', 'Jurídica']);
            $table->string('cpf_cnpj');
            $table->string('banco');
            $table->string('agencia')->nullable();
            $table->string('conta');
            $table->enum('tipo_conta', ['Conta Corrente', 'Poupança', 'Conta Pagamento']);
            $table->boolean('ativo')->default(1);

            $table->bigInteger('id_cliente')->unsigned();
            $table->foreign('id_cliente')->references('id')->on('clientes');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('lifepet_plus_solicitacoes', function(Blueprint $table) {
            $table->dropColumn('reembolso_banco');
            $table->dropColumn('reembolso_tipo_conta');
            $table->dropColumn('reembolso_titularidade');
            $table->dropColumn('reembolso_nome_completo');
            $table->dropColumn('reembolso_agencia');
            $table->dropColumn('reembolso_conta');
            $table->dropColumn('reembolso_cpf');

            $table->integer('conta_bancaria_id')->unsigned();
            $table->foreign('conta_bancaria_id')->references('id')->on('clientes_contas_bancarias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lifepet_plus_solicitacoes', function(Blueprint $table) {
            $table->string('reembolso_banco');
            $table->string('reembolso_tipo_conta');
            $table->string('reembolso_titularidade');
            $table->string('reembolso_nome_completo');
            $table->string('reembolso_agencia');
            $table->string('reembolso_conta');
            $table->string('reembolso_cpf');
            $table->dropForeign(['conta_bancaria_id']);
            $table->dropColumn('conta_bancaria_id');
        });

        Schema::drop('clientes_contas_bancarias');
    }
}
