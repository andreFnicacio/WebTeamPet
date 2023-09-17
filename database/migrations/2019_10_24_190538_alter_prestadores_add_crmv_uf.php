<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPrestadoresAddCrmvUf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prestadores', function(Blueprint $table) {
            $table->string('crmv_uf')->default('ES')->after('crmv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestadores', function(Blueprint $table) {
            $table->dropColumn('crmv_uf');
        });
    }
}
