<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLifepetPlusOrcamentosTelefoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lifepet_plus_orcamentos', function(Blueprint $table) {
            $table->string('telefone', 20)
                ->after('email')
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
        Schema::table('lifepet_plus_orcamentos', function(Blueprint $table) {
            $table->dropColumn('telefone');
        });
    }
}
