<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColaboradoresCargosAddDataEncerramento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colaboradores_cargos', function(Blueprint $table) {
            $table->date('data_encerramento')->after('data_inicio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colaboradores_cargos', function(Blueprint $table) {
            $table->dropColumn('data_encerramento');
        });
    }
}
