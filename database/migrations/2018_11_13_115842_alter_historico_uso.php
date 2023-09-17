<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHistoricoUso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            $table->enum('glosado', [0, 1, 2, 3])
                ->comment('0 - Não, 1 - Sim, 2 - Revertido, 3 - Confirmado')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            $table->dropColumn('glosado');
        });
    }
}
