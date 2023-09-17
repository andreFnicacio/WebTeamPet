<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCompraRapidaAddRetry extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::table('lifepet_compra_rapida', function(Blueprint $table) {
            $table->integer('retries')->default(0);
            $table->timestamp('last_retry')->nullable();
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
            $table->dropColumn('retries');
            $table->dropColumn('last_retry');
        });
    }
}
