<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsPlanosAddIdContratoSuperlogica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets_planos', function (Blueprint $table) {
            $table->string('id_contrato_superlogica')->nullable()->comment('Coluna que identifica o ID de contratação da assinatura no Superlogica');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets_planos', function (Blueprint $table) {
            $table->dropColumn('id_contrato_superlogica');
        });
    }
}
