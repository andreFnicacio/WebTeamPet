<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLifepetCompraRapidaAddIdCupom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lifepet_compra_rapida', function(Blueprint $table) {
            $table->integer('id_cupom')->unsigned()->nullable();

            $table->foreign('id_cupom')->references('id')->on('lpt__codigos_promocionais');
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
            $table->dropColumn('id_cupom');
        });
    }
}
