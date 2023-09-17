<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCobrancasAddOldSuperlogicaId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cobrancas', function(Blueprint $table) {
            $table->string('old_superlogica_id');
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
            $table->dropColumn('old_superlogica_id');
        });
    }
}
