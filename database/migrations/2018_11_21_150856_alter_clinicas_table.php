<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClinicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinicas', function(Blueprint $table) {
            $table->boolean('ativo')
                ->default(1)
                ->comment('0 - Inativo, 1 - Ativo');
            $table->boolean('exibir_site')
                ->default(1)
                ->comment('0 - Não, 1 - Sim');
            $table->string('nome_site')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
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
            $table->dropColumn('ativo');
            $table->dropColumn('exibir_site');
            $table->dropColumn('nome_site');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
    }
}
