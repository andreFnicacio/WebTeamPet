<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDespesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despesas', function(Blueprint $table) {
            $table->increments('id');
            $table->double('valor_total');
            $table->string('descricao');
            $table->double('porcentagem_participacao');
            $table->integer('id_centrocusto')->unsigned();
            $table->double('valor');
            $table->string('observacoes')->nullable();
            $table->string('label')->nullable();
            $table->string('historico');
            $table->date('data_emissao');
            $table->date('data_ordenacao');
            $table->date('data_previsaocredito');

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
        Schema::drop('despesas');
    }
}
