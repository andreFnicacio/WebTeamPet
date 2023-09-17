<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePlanosAddIsento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->boolean('isento')->default(0)->comment('Dessa forma, a Lifepet não cobrará no plano. Será pago para a credenciada.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planos', function(Blueprint $table) {
            $table->dropColumn('isento');
        });
    }
}
