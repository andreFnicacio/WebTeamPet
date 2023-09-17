<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanosProcedimentosAddVantagens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos_procedimentos', function(Blueprint $table) {
            $table->enum('beneficio_tipo', ['fixo', 'percentual'])->nullable();
            $table->double('beneficio_valor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planos_procedimentos', function(Blueprint $table) {
            $table->dropColumn('beneficio_tipo');
            $table->dropColumn('beneficio_valor');
        });
    }
}
