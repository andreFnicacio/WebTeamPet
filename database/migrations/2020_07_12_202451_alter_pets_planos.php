<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsPlanos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets_planos', function(Blueprint $table) {
            $table->boolean('desconto_folha')->default(0)->comment('0: não desconta. 1: desconta');
            $table->integer('id_conveniada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets_planos', function(Blueprint $table) {
            $table->dropColumn('desconto_folha');
            $table->dropColumn('id_conveniada');
        });
    }
}
