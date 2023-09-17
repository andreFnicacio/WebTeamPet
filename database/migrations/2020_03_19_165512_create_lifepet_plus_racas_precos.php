<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifepetPlusRacasPrecos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_racas_precos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_raca')->unsigned();
            $table->decimal('preco_macho', 10, 2);
            $table->decimal('preco_femea', 10, 2);
            $table->decimal('reembolso_porcentagem', 10, 2);
            $table->foreign('id_raca')->references('id')->on('racas');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lifepet_plus_racas_precos');
    }
}
