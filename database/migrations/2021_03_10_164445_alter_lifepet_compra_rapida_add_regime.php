<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLifepetCompraRapidaAddRegime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lifepet_compra_rapida', function(Blueprint $table) {
            $table->string('regime')->default('MENSAL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lifepet_compra_rapida', function(Blueprint $table) {
            $table->dropColumn('regime');
        });
    }
}
