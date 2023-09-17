<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableConveniadasAddIdExterno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conveniadas', function(Blueprint $table) {
            $table->integer('id_externo')->nullable();
            $table->integer('data_vencimento')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conveniadas', function(Blueprint $table) {
            $table->dropColumn('id_externo');
            $table->dropColumn('data_vencimento');
        });
    }
}
