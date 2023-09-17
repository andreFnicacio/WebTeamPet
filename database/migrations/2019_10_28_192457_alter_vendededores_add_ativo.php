<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVendededoresAddAtivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendedores', function(Blueprint $table) {
            $table->boolean('ativo')
                  ->default(0)
                  ->comment('0 - Inativo, 1 - Ativo');
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
            $table->dropColumn('ativo');
        });
    }
}
