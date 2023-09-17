<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDadosTemporaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dados_temporais', function(Blueprint $table) {
            $table->increments('id');
            $table->string('indicador');
            $table->string('tipo_valor');
            $table->double('valor_numerico')->default(0);
            $table->string('valor_textual')->nullable();
            $table->date('data_referencia');
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
        Schema::drop('dados_temporais');
    }
}
