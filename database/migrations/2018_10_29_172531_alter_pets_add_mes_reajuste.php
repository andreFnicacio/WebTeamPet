<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsAddMesReajuste extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->enum('mes_reajuste', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])
                ->nullable()
                ->comment('Mês em que será renovado o período do contato');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('mes_reajuste');
        });
    }
}
