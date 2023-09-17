<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHistoricoUsoAddAssinaturas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            $table->string('assinatura_cliente')->nullable();
            $table->dateTime('data_assinatura_cliente')->nullable();
            $table->enum('meio_assinatura_cliente', [1, 2, 3])
                ->nullable()
                ->comment('Meio onde o cliente assinou a guia. 1=Sistema, 2=Aplicativo, 3=Presencial');

            $table->string('assinatura_prestador')->nullable();
            $table->dateTime('data_assinatura_prestador')->nullable();
        });

        $guias = (new \Modules\Guides\Entities\HistoricoUso())->whereNull('meio_assinatura_cliente')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('tipo_atendimento', '!=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO);
                    $query->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO);
                });
                $query->orWhere(function ($query) {
                    $query->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO);
                    $query->whereNotNull('realizado_em');
                });
            })
            ->update(['meio_assinatura_cliente' => \Modules\Guides\Entities\HistoricoUso::MEIO_ASSINATURA_PRESENCIAL]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            $table->dropColumn('assinatura_cliente');
            $table->dropColumn('data_assinatura_cliente');
            $table->dropColumn('meio_assinatura_cliente');
            $table->dropColumn('assinatura_prestador');
            $table->dropColumn('data_assinatura_prestador');
        });
    }
}
