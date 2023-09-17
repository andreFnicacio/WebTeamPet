<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuiasGlosasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guias_glosas', function(Blueprint $table) {
            $table->increments('id');

            $table->text('justificativa');
            $table->text('defesa')->nullable();
            $table->dateTime('data_defesa')->nullable();
            $table->text('justificativa_confirmacao')->nullable();
            $table->dateTime('data_confirmacao')->nullable();

            $table->unsignedInteger('id_historico_uso');
            $table->unsignedInteger('id_usuario');

            $table->foreign('id_historico_uso')->references('id')->on('historico_uso');
            $table->foreign('id_usuario')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('guias_glosas');
    }
}
