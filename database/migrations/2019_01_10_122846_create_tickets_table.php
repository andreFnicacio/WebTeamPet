<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function(Blueprint $table) {
            $table->increments('id');
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('categoria', ['Dúvida', 'Problema', 'Sistema', 'Sugestão', 'Outros']);
            $table->enum('status', ['Aberto', 'Em Andamento', 'Finalizado'])->default('Aberto');
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_finalizacao')->nullable();
            $table->dateTime('previsao_finalizacao')->nullable();
            $table->enum('gravidade', [1,2,3,4,5])->default(1);
            $table->enum('urgencia', [1,2,3,4,5])->default(1);
            $table->enum('tendencia', [1,2,3,4,5])->default(1);
            $table->unsignedTinyInteger('ordem');

            $table->integer('id_solicitante')->unsigned()->comment('Usuário que solicitou o ticket');
            $table->foreign('id_solicitante')->references('id')->on('colaboradores');

            $table->integer('id_departamento')->unsigned();
            $table->foreign('id_departamento')->references('id')->on('departamentos');

            $table->integer('id_atribuicao')->unsigned()->nullable()->comment('Usuário atribuído ao ticket');
            $table->foreign('id_atribuicao')->references('id')->on('colaboradores');

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
        Schema::drop('tickets');
    }
}
