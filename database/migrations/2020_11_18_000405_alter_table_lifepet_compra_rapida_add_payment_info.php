<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLifepetCompraRapidaAddPaymentInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lifepet_compra_rapida', function(Blueprint $table) {
            $table->longText('pagamentos')->nullable()->comment('Array serializado contendo os dados de tentativa de pagamento.');
            $table->boolean('pagamento_confirmado')->nullable()->comment('Indica se o pagamento foi confirmado ou não.');
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
            $table->dropColumn('pagamentos');
            $table->dropColumn('pagamento_confirmado');
        });
    }
}
