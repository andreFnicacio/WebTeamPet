<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanosTableAddPontos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->float('pontos')
                ->unsigned()
                ->comment('Determina quantos pontos vale aquele plano.')
                ->nullable()
                ->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->dropColumn('pontos');
        });
    }
}
