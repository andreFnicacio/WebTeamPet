<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarteiraDigitalTransacoesMotivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carteira_digital_transacoes_motivos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('nome', 250);
            $table->unsignedInteger('created_by');

            $table->index(["created_by"], 'fk_carteira_digital_transacoes_motivos_users1_idx');
            $table->timestamps();


            $table->foreign('created_by', 'fk_carteira_digital_transacoes_motivos_users1_idx')
                ->references('id')->on('users')
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
        Schema::dropIfExists('carteira_digital_transacoes_tipo');
    }
}
