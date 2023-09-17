<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHistoricoUsoNumeroGuia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE historico_uso MODIFY numero_guia varchar(255);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico_uso', function(Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE historico_uso MODIFY numero_guia integer(10) unsigned;");
        });
    }
}
