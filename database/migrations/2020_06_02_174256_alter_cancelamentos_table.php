<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCancelamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cancelamentos', function(Blueprint $table) {
            $table->boolean('cancelar_externo')->default(0)->comment('Se cancela no sistema financeiro ou não');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cancelamentos', function(Blueprint $table) {
            $table->dropColumn('cancelar_externo');
            
        });
    }
}
