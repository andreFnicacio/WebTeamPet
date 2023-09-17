<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLptCodigosPromocionaisAddAplicabilidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpt__codigos_promocionais', function (Blueprint $table) {
            $table->enum('aplicabilidade', ['T','A','M'])->default('T')->comment('T - Todos, A - Anual, M - Mensal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lpt__codigos_promocionais', function (Blueprint $table) {
            $table->dropColumn('aplicabilidade');
        });
    }
}
