<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioPetsPlanosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relatorio_pets_planos', function (Blueprint $table) {
            
            $table->increments('id');

            $table->date('data');
            $table->integer('qtde_total');

            $table->integer('qtde_total_iniciados')->comments('Total de pets com data de inicio de contrato até a data');
            $table->decimal('valor_total_iniciados', 10, 2)->comments('Total em valor (mensal) dos contratos iniciados até a data');
            
            $table->integer('qtde_total_encerrados')->comments('Total de pets com data de encerramento de contrato até a data');
            $table->decimal('valor_total_encerrados', 10, 2)->comments('Total em valor (mensal) dos contratos encerrados até a data');

            $table->integer('qtde_dia_iniciados')->comments('Total de pets que iniciaram o contrato no dia');
            $table->decimal('valor_dia_iniciados', 10, 2)->comments('Total em valor (mensal) dos contratos iniciados no dia');

            $table->integer('qtde_dia_encerrados')->comments('Total de pets que encerraram o contrato no dia');
            $table->decimal('valor_dia_encerrados', 10, 2)->comments('Total em valor (mensal) dos contratos encerrados no dia');

            $table->integer('qtde_dia_downgrades')->comments('Total de pets que fizeram upgrade por dia');
            $table->integer('qtde_dia_upgrades')->comments('Total de pets que fizeram upgrade por dia');


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
        Schema::drop('relatorio_pets_planos');
    }
}
