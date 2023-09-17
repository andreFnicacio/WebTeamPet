<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class Planos.
 *
 * @author  The scaffold-interface created at 2017-07-31 06:12:03pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class Planos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('planos',function (Blueprint $table){

            $table->increments('id');
            
            $table->string('nome_plano');

            /**
             * Dados apenas para referência. Os valores reais são buscados do Superlógica
             */
            $table->double('preco_plano_familiar', 15, 4);
            $table->double('preco_plano_individual', 15, 4);
            
            $table->date('data_vigencia');
            
            $table->date('data_inatividade')->nullable();
            
            $table->boolean('ativo')->default(0);
            $table->boolean('bichos')->default(0);

            /**
             * Foreignkeys section
             */
            
            
            $table->timestamps();
            // type your addition here

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('planos');
    }
}
