<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicasAtendimentosTagsSelecionadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinicas_atendimentos_tags_selecionadas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('clinica_id');
            $table->unsignedInteger('clinica_atendimento_tag_id');
            $table->unsignedInteger('created_by');

            $table->index(["created_by"], 'fk_cli_at_tags_selecionadas_users1_idx');
            $table->timestamps();

            $table->foreign('created_by', 'fk_cli_at_selecionadas_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('clinica_id', 'fk_cli_at_tags_selecionadas_clinicas1_idx')
                ->references('id')->on('clinicas')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('clinica_atendimento_tag_id', 'fk_cli_at_tags_selecionadas_cli_at_tags1_idx')
                ->references('id')->on('clinicas_atendimentos_tags')
                ->onDelete('no action')
                ->onUpdate('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinicas_atendimentos_tags_selecionadas');
    }
}
