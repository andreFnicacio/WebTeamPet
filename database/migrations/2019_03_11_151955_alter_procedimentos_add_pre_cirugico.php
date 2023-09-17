<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProcedimentosAddPreCirugico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procedimentos', function(Blueprint $table) {
            $table->boolean('pre_cirurgico')
                ->comment('Informa se o procedimento é pré-cirúrgico')
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
        Schema::table('procedimentos', function(Blueprint $table) {
            $table->dropColumn('pre_cirurgico');
        });
    }
}
