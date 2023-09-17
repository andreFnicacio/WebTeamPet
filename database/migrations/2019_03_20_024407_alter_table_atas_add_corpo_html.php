<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAtasAddCorpoHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('atas', function(Blueprint $table) {
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
        Schema::table('atas', function(Blueprint $table) {
            $table->dropColumn('corpo_html');
        });
    }
}
