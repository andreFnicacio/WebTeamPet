<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterParticipacaoTableAddAgendamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participacao', function(Blueprint $table) {
            $table->date('agendado')->nullable();
            $table->dateTime('executado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participacao', function(Blueprint $table) {
            $table->dropColumn(['agendado', 'executado']);
        });
    }
}
