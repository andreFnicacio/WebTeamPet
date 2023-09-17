<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClinicasAddEmailTelefoneSite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinicas', function(Blueprint $table) {
            $table->string('telefone_site')->nullable()->after('nome_site');
            $table->string('email_site')->nullable()->after('telefone_site');
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
            $table->dropColumn('telefone_site');
            $table->dropColumn('email_site');
        });
    }
}
