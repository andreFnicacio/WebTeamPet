<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentosInternosAddCupom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documentos_internos', function (Blueprint $table) {
            $table->integer('id_cupom', false, true)->nullable();
            $table->foreign('id_cupom')->references('id')->on('lpt__codigos_promocionais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentos_internos', function (Blueprint $table) {
            $table->dropColumn('id_cupom');
        });
    }
}
