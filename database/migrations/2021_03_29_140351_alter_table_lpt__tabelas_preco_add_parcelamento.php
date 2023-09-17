<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLptTabelasPrecoAddParcelamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpt__tabelas_preco', function(Blueprint $table) {
            $table->integer('parcelas')->unsigned()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lpt__tabelas_preco', function(Blueprint $table) {
            $table->dropColumn('parcelas');
        });
    }
}
