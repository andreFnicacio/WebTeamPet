<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClinicasAddRestricaoProcedimentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinicas', function (Blueprint $table) {
            $table->boolean('restricao_procedimentos')->default(0)->comment('Determina se a clínica terá ou não liberdade para escolher procedimentos que não pertencem ao plano corrente.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinicas', function (Blueprint $table) {
            $table->dropColumn('restricao_procedimentos');
        });
    }
}
