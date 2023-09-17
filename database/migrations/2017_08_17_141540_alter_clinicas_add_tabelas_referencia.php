<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClinicasAddTabelasReferencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinicas', function(Blueprint $table) {
            $table->integer('id_tabela')->unsigned();
            $table->foreign('id_tabela')->references('id')->on('tabelas_referencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinicas', function(Blueprint $table) {
            $table->dropColumn('id_tabela');
            $table->dropForeign('id_tabela');
        });
    }
}
