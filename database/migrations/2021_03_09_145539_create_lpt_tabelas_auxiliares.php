<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLptTabelasAuxiliares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lpt__codigos_promocionais', function(Blueprint $table) {
           $table->increments('id');
           $table->string('codigo');
           $table->dateTime('expira_em');
           $table->float('desconto');
           $table->integer('id_plano')->unsigned();
           $table->timestamps();

           $table->unique(['codigo', 'id_plano']);

           $table->foreign('id_plano')->references('id')->on('planos');
        });

        Schema::create('lpt__configuracoes', function(Blueprint $table) {
            $table->boolean('ativar_promocoes')->default(1);
            $table->boolean('permitir_anuais')->default(1);
        });

        Schema::create('lpt__tabelas_preco', function(Blueprint $table) {
           $table->increments('id');
           $table->integer('id_plano')->unsigned();
           $table->integer('pets')->unsigned();
           $table->float('preco')->unsigned();
           $table->integer('id_usuario')->unsigned();
            $table->string('regime')->default('MENSAL');
           $table->timestamps();

           $table->foreign('id_plano')->references('id')->on('planos');
           $table->foreign('id_usuario')->references('id')->on('users');

           $table->unique(['id_plano', 'pets', 'regime']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lpt__codigos_promocionais');
        Schema::drop('lpt__configuracoes');
        Schema::drop('lpt__tabelas_preco');
    }
}
