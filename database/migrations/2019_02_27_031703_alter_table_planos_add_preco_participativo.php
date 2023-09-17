<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePlanosAddPrecoParticipativo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->float('preco_participativo')
                ->unsigned()
                ->comment('Valor do plano participativo.')
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
        Schema::table('planos', function(Blueprint $table) {
            $table->dropColumn('preco_participativo');
        });
    }
}
