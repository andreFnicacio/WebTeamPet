<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLifepetCompraRapida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lifepet_compra_rapida', function(Blueprint $table) {
            $table->integer('id_plano')->nullable()->comment('Determina qual será o ID do plano');
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
            $table->dropColumn('id_plano');
        });
    }
}
