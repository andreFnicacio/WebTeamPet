<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCobrancasIdFinanceiroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cobrancas', function(Blueprint $table) {
            $table->integer('id_financeiro')->nullable()->comment('payment_id - pertence ao id do pagamento do sistema financeiro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cobrancas', function(Blueprint $table) {
            $table->dropColumn('id_financeiro');
        });
    }
}
