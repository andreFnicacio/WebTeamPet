<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsPlanosAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets_planos', function (Blueprint $table) {
            $table->enum('status', ["P", "U", "D", "R"])
                ->nullable()
                ->comment('P = Primeiro Plano, U = Updgrade, D = Downgrade, R = Renovação, A = Alteração');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets_planos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}