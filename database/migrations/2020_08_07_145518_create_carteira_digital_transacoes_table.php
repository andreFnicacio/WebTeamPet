<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarteiraDigitalTransacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carteira_digital_transacoes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->bigInteger('cliente_id')->unsigned();
            $table->integer('transacao_motivo_id')->unsigned();
            $table->decimal('valor', 10, 2);
            $table->enum('tipo', ['1', '2'])->comment('1 = Débito; 2 = Crédito');
            $table->string('descricao', 250)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            

            $table->foreign('cliente_id', 'fk_carteira_digital_transacoes_clientes_idx')
                ->references('id')->on('clientes')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('transacao_motivo_id', 'fk_carteira_digital_transacoes_motivo_idx')
                ->references('id')->on('carteira_digital_transacoes_motivos')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->index(["cliente_id"], 'fk_carteira_digital_transacoes_clientes_idx');
            $table->index(["transacao_motivo_id"], 'fk_carteira_digital_transacoes_motivo_idx');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carteira_digital_transacoes');
    }
}
