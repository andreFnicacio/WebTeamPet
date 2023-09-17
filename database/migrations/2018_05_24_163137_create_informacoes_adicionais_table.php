<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformacoesAdicionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informacoes_adicionais', function(Blueprint $table) {
            $table->increments('id');
            $table->string('cor');
            $table->string('descricao_resumida')->nullable();
            $table->text('descricao_completa');
            $table->string('icone')->nullable();
            $table->integer('prioridade');
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
        Schema::drop('informacoes_adicionais');
    }
}
