<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnunsedColumsClinicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinicas', function(Blueprint $table) {
            $table->dropColumn('bichos_habilitado');
            $table->dropColumn('percentual_exclusividade');
            $table->dropColumn('restricao_procedimentos');
            $table->dropColumn('crmv');
            $table->dropColumn('complemento_endereco');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
