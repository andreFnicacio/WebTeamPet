<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanosGruposAddUsoUnico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos_grupos', function (Blueprint $table) {
            $table->boolean('uso_unico')->default(false)->comment('Coluna que identifica se os procedimentos podem ser utilizados apenas uma única vez na vida do pet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planos_grupos', function (Blueprint $table) {
            $table->dropColumn('uso_unico');
        });
    }
}
