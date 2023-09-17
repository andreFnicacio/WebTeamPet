<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPrestadoresAddDataFormacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prestadores', function(Blueprint $table) {
            $table->date('data_formacao')
                ->nullable()
                ->comment('Data em que o prestador se formou.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestadores', function(Blueprint $table) {
            $table->dropColumn('data_formacao');
        });
    }
}
