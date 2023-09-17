<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsPlanosAddIdPrimeiraCobrancaSuperlogica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets_planos', function (Blueprint $table) {
            $table->string('id_primeira_cobranca_superlogica')->nullable();
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
            $table->removeColumn('id_primeira_cobranca_superlogica');
        });
    }
}
