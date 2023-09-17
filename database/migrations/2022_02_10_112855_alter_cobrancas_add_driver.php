<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCobrancasAddDriver extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            $table->enum('driver', ['SUPERLOGICA_V1', 'SUPERLOGICA_V2', 'SISTEMA_FINANCEIRO'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            $table->dropColumn('driver');
        });
    }
}
