<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsPlanosAddAdesao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets_planos', function(Blueprint $table) {
            $table->float('adesao')
                ->unsigned()
                ->comment('Determina se houve e quanto foi a adesão.')
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
        Schema::table('pets_planos', function(Blueprint $table) {
            $table->dropColumn('adesao');
        });
    }
}
