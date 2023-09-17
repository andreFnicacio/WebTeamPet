<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifepetPlusAssinaturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_assinaturas', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('lifepet_plus_cliente_id')->unsigned();
            $table->integer('pet_id')->unsigned();
            $table->enum('status_assinatura', ['PENDENTE', 'VIGENTE', 'ENCERRADO']);
            $table->datetime('data_assinatura');
            $table->datetime('data_inicio')->nullable();
            $table->datetime('data_encerramento')->nullable();
            $table->smallInteger('carencia');
            $table->enum('meio', ['1', '2', '3'])->comments('Campo que salva por onde o cliente se cadastrou: 1 - Sistema, 2 - App, 3 - Website');
            $table->decimal('porcentagem', 10, 2);
            $table->decimal('valor', 10, 2);
            $table->enum('regime', ['MENSAL', 'ANUAL']);
            $table->decimal('total_contratado', 10, 2);
            $table->decimal('total_gasto', 10, 2);
            $table->decimal('limite_solicitacao', 10, 2);

            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();

            $table->foreign('lifepet_plus_cliente_id', 'lpc_id_foreign')->references('id')->on('lifepet_plus_clientes');

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
        Schema::drop('lifepet_plus_assinaturas');
    }
}
