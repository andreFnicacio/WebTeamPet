<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRenovacoesAddCompetencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('renovacoes', function(Blueprint $table) {
            $table->string('competencia_mes');
            $table->string('competencia_ano');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovacoes', function(Blueprint $table) {
           $table->dropColumn('competencia_mes');
           $table->dropColumn('competencia_ano');
        });
    }
}
