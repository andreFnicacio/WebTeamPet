<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanosAddFaixa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->integer('id_faixa')->unsigned()->nullable();
            $table->foreign('id_faixa')->references('id')->on('faixas_planos');
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
            $table->dropForeign('id_faixa');
            $table->dropColumn('id_faixa');
        });
    }
}
