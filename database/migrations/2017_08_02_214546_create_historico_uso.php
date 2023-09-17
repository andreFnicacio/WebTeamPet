<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricoUso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico_uso', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pet')->unsigned();
            $table->integer('id_procedimento')->unsigned();
            $table->integer('id_plano')->unsigned();
            $table->integer('id_prestador')->unsigned();
            $table->integer('id_clinica')->unsigned();
            $table->integer('id_especialidade')->unsigned()->nullable();
            $table->integer('numero_guia')->unsigned();
            $table->double('valor_momento', 15, 4);
            $table->text('justificativa')->nullable();
            $table->text('laudo')->nullable();
            $table->text('observacao')->nullable();
            $table->string('imagem_laudo')->nullable();
            $table->enum('autorizacao', ["AUTOMATICA", "AUDITORIA", "FORCADO"]);
            $table->enum('tipo_atendimento', ["NORMAL", "EMERGENCIA"]);
            $table->enum('status', ["LIBERADO", "RECUSADO", "AVALIACAO"]);
            $table->integer('id_solicitador');
            $table->dateTime('data_liberacao');
            $table->dateTime('realizado_em');
            $table->string('cancelamento');

            /*
             * Foreign Keys
             */
            
            $table->foreign('id_pet')->references('id')->on('pets');
            $table->foreign('id_procedimento')->references('id')->on('procedimentos');
            $table->foreign('id_plano')->references('id')->on('planos');
            $table->foreign('id_prestador')->references('id')->on('prestadores');
            $table->foreign('id_clinica')->references('id')->on('clinicas');
            $table->foreign('id_especialidade')->references('id')->on('especialidades');

            //Timestamps (CREATED_AT, UPDATED_AT)
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
        Schema::drop('historico_uso');
    }
}
