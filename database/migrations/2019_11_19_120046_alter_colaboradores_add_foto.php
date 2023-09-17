<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColaboradoresAddFoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colaboradores', function(Blueprint $table) {
            $table->string('foto')
                ->comment('Path do arquivo de foto do colaborador')
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
        Schema::table('colaboradores', function(Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
}
