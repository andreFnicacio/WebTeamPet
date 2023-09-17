<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProcedimentosAddGrupo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procedimentos', function(Blueprint $table) {
            $table->integer('id_grupo')
                ->unsigned()
                ->comment('Chave que fará a relação entre o procedimento e seu grupo. Apenas 1 grupo por procedimento.')
                ->change();

            $table->foreign('id_grupo')->references('id')->on('grupos_carencias');
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
            $table->dropForeign('id_grupo');
        });
    }
}
