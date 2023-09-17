<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTreinamentosAddCorpoHtml extends Migration
{
    public function up()
    {
        Schema::table('treinamentos', function(Blueprint $table) {
            $table->text('corpo_html')
                ->comment('Conteúdo gerado pelo editor em HTML');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('treinamentos', function(Blueprint $table) {
            $table->dropColumn('corpo_html');
        });
    }
}
