<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientesFarmapetAddLimiteMensal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function(Blueprint $table) {
            $table->decimal('limite_mensal_farmapet', 13, 2)->nullable();
            $table->decimal('saldo_contratado_farmapet', 13, 2)->nullable()->after('saldo_farmapet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function(Blueprint $table) {
            $table->dropColumn('limite_mensal_farmapet');
            $table->dropColumn('saldo_contratado_farmapet');
        });
    }
}
