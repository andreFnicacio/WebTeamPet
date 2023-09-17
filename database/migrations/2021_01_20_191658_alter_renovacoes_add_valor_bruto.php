<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRenovacoesAddValorBruto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('renovacoes', function(Blueprint $table) {
            $table->double('valor_bruto')->nullable();
            $table->double('reajuste')->nullable();
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
            $table->dropColumn('valor_bruto');
            $table->dropColumn('reajuste');
        });
    }
}
