<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVendedoresAddDiretoField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendedores', function(Blueprint $table) {
            $table->boolean('direto')
                ->comment('Informa se é ou não um vendedor direto da Lifepet')
                ->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendedores', function(Blueprint $table) {
            $table->dropColumn('direto');
        });
    }
}
