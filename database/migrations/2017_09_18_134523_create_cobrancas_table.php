<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobrancasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobrancas', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('id_cliente')->unsigned();
            $table->integer('id_superlogica')->comment('ID para a sincronização com o superlógica via Webhook');
            $table->string('competencia', 7);
            $table->double('valor_original');
            $table->date('data_vencimento');
            $table->text('complemento')->nullable();

            $table->tinyInteger('status')->default(1)
                                         ->comment('0 - Cancelado, 1 - Ativo');

            //Timestamps (CREATED_AT, UPDATED_AT)
            $table->timestamps();
            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cobrancas');
    }
}
