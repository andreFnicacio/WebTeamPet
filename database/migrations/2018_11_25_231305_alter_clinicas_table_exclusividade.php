<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClinicasTableExclusividade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinicas', function(Blueprint $table) {
            $table->double('percentual_exclusividade')
                ->comment('Valor a partir de 0 que define o percentual de adição de acordo com a exclusividade')
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
        Schema::table('clinicas', function(Blueprint $table) {
            $table->dropColumn('percentual_exclusividade');
        });
    }
}
