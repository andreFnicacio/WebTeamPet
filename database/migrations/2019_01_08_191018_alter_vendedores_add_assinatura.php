<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVendedoresAddAssinatura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendedores', function(Blueprint $table) {
            $table->string('assinatura')
                ->comment('Path do arquivo de assinatura digital do vendedor')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendedores', function(Blueprint $table) {
            $table->dropColumn('assinatura');
        });
    }
}
