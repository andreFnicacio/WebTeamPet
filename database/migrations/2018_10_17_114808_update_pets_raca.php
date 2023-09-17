<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePetsRaca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets', function(Blueprint $table) {
            $table->integer('id_raca')
                ->unsigned()
                ->comment('Chave que fará a relação entre o pet e sua raça.')->default(1);

            $table->foreign('id_raca')->references('id')->on('racas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets', function(Blueprint $table) {
            $table->dropForeign('id_raca');
            $table->dropColumn('id_raca');
        });
    }
}
