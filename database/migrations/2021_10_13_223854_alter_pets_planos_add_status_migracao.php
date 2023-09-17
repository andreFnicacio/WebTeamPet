<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsPlanosAddStatusMigracao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets_planos', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE pets_planos MODIFY status ENUM('P', 'U', 'D', 'R', 'A', 'M')");
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
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE pets_planos MODIFY status ENUM('P', 'U', 'D', 'R', 'A')");
        });
    }
}
