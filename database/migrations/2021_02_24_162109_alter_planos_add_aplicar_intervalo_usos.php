<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanosAddAplicarIntervaloUsos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->boolean('aplicar_intervalo_usos')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->dropColumn('aplicar_intervalo_usos');
        });
    }
}
