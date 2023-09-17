<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableConveniadasFaturamentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('conveniadas_faturamentos')) {
            Schema::create('conveniadas_faturamentos', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('id_conveniada')->unsigned();
                $table->string('competencia');
                $table->date('vencimento');
                $table->string('hash')->nullable();
                $table->string('status');
                $table->integer('id_externo')->nullable();

                $table->timestamps();

                $table->foreign('id_conveniada')->references('id')->on('conveniadas')->onDelete('cascade');
            });
        }

        Schema::create('conveniadas_faturamentos_itens', function(Blueprint $table) {
           $table->increments('id');
           $table->integer('id_fatura_conveniada')->unsigned();
           $table->bigInteger('id_cliente')->unsigned();
           $table->integer('id_pet')->unsigned();
           $table->integer('id_plano')->unsigned();
           $table->float('valor');
           $table->string('tipo')->default('BOLETO');
           $table->string('descricao')->nullable();

           $table->timestamps();

           $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('cascade');
           $table->foreign('id_pet')->references('id')->on('pets')->onDelete('cascade');
           $table->foreign('id_plano')->references('id')->on('planos')->onDelete('cascade');
           $table->foreign('id_fatura_conveniada')->references('id')->on('conveniadas_faturamentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('conveniadas_faturamentos_itens');
        Schema::drop('conveniadas_faturamentos');
    }
}
