<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHistoricoUsoAddPrestadorSolicitante extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            $table->unsignedInteger('id_prestador_solicitante')
                ->comment('Id do prestador que solicitou o encaminhamento')
                ->nullable();

            $table->foreign('id_prestador_solicitante')->references('id')->on('prestadores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            $table->dropColumn('id_prestador_solicitante');
        });
    }
}
