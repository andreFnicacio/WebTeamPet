<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePetsPlano extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('id_plano');

            $table->integer('id_pets_planos')
                ->unsigned()
                ->comment('Chave que fará a relação entre o pet e seu plano atual em "pets_planos".')
                ->nullable();

            $table->foreign('id_pets_planos')->references('id')->on('pets_planos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->integer('id_plano')->unsigned()->nullable();
            $table->dropForeign('id_pets_planos');
            $table->dropColumn('id_pets_planos');
        });
    }
}
