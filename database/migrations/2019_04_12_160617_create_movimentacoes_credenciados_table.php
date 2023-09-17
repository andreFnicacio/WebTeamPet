<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimentacoesCredenciadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimentacoes_credenciados', function(Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('tipo');
            $table->string('descricao');
            $table->decimal('valor', 5, 2);
            $table->unsignedInteger('estrelas');

            $table->boolean('pago')->default(0);

            $table->integer('id_clinica')->unsigned();
            $table->foreign('id_clinica')->references('id')->on('clinicas');

            $table->integer('id_guia_consulta')->unsigned();
            $table->foreign('id_guia_consulta')->references('id')->on('historico_uso');

            $table->integer('id_guia_origem')->unsigned()->nullable();
            $table->foreign('id_guia_origem')->references('id')->on('historico_uso');

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
        Schema::drop('movimentacoes_credenciados');
    }
}
