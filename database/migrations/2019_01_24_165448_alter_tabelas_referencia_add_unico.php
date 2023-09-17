<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTabelasReferenciaAddUnico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tabelas_referencia', function(Blueprint $table) {
            $table->boolean('tabela_base')
                ->comment('Informa se a tabela é a base de referência para as outras tabelas')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabelas_referencia', function(Blueprint $table) {
            $table->dropColumn('tabela_base');
        });
    }
}
