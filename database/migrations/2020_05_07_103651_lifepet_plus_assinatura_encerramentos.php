<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LifepetPlusAssinaturaEncerramentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_assinatura_encerramentos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('lifepet_plus_assinatura_id')->unsigned();
            $table->enum('motivo', ['INADIMPLENCIA', 'INTERESSE', 'FINANCEIRO', 'DESCONTENTAMENTO', 'JURIDICO', 'OBITO', 'OUTROS']);
            $table->text('justificativa');
            $table->datetime('data_encerramento');
            $table->integer('created_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lifepet_plus_assinatura_encerramentos');
    }
}
