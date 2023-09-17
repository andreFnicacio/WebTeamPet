<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifepetPlusSolicitacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_solicitacoes', function(Blueprint $table) {
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

            $table->text('descricao')->nullable();
            $table->text('mensagem_auditoria')->nullable();
            $table->text('mensagem_financeiro')->nullable();
            $table->text('comprovante_pagamento')->nullable();

            $table->bigInteger('cliente_id')->nullable()->unsigned();
            $table->integer('pet_id')->nullable()->unsigned();
            
            $table->integer('lifepet_plus_assinatura_id')->nullable()->unsigned();
            $table->integer('lifepet_plus_cliente_id')->nullable()->unsigned();

            $table->dateTime('data_procedimento');
            $table->dateTime('data_analise')->nullable();
            $table->dateTime('data_aprovacao_auditoria')->nullable();
            $table->dateTime('data_recusa_auditoria')->nullable();
            $table->dateTime('data_aprovacao_financeiro')->nullable();
            $table->dateTime('data_recusa_financeiro')->nullable();
            $table->dateTime('data_pagamento')->nullable();
            $table->dateTime('data_cancelamento')->nullable();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('pet_id')->references('id')->on('pets');

            $table->foreign('lifepet_plus_assinatura_id')->references('id')->on('lifepet_plus_assinaturas');
            $table->foreign('lifepet_plus_cliente_id')->references('id')->on('lifepet_plus_clientes');

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
        Schema::drop('lifepet_plus_solicitacoes');
    }
}
