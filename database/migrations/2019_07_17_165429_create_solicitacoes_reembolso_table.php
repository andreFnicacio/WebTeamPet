<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitacoesReembolsoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitacoes_reembolso', function(Blueprint $table) {
            $table->increments('id');

            $table->enum('status', [
                "ABERTO",
                "CANCELADO",
                "EM ANÁLISE",
                "RECUSADO_AUDITORIA",
                "APROVADO_AUDITORIA",
                "RECUSADO_FINANCEIRO",
                "APROVADO_FINANCEIRO",
                "PAGO"
            ])->default("ABERTO");

            $table->string('reembolso_banco');
            $table->string('reembolso_tipo_conta');
            $table->string('reembolso_titularidade');
            $table->string('reembolso_nome_completo');
            $table->string('reembolso_agencia');
            $table->string('reembolso_conta');
            $table->string('reembolso_cpf');

            $table->string('descricao')->nullable();
            $table->string('mensagem_auditoria')->nullable();
            $table->string('mensagem_financeiro')->nullable();
            $table->string('comprovante_pagamento')->nullable();

            $table->bigInteger('id_cliente')->nullable()->unsigned();
            $table->integer('id_pet')->nullable()->unsigned();

            $table->dateTime('data_procedimento');
            $table->dateTime('data_analise')->nullable();
            $table->dateTime('data_aprovacao_auditoria')->nullable();
            $table->dateTime('data_recusa_auditoria')->nullable();
            $table->dateTime('data_aprovacao_financeiro')->nullable();
            $table->dateTime('data_recusa_financeiro')->nullable();
            $table->dateTime('data_pagamento')->nullable();
            $table->dateTime('data_cancelamento')->nullable();

            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_pet')->references('id')->on('pets');

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
        Schema::drop('solicitacoes_reembolso');
    }
}
