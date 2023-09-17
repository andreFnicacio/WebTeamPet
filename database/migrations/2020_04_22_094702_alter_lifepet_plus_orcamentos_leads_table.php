<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLifepetPlusOrcamentosLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lifepet_plus_orcamentos', function(Blueprint $table) {
            $table->boolean('lead')
                ->after('ip')
                ->default(false)
                ->comments('Campo que indica se foi enviado como lead por e-mail');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lifepet_plus_orcamentos', function(Blueprint $table) {
            $table->dropColumn('lead');
        });
    }
}
