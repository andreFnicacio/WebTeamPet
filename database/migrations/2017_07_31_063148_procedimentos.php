<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class Procedimentos.
 *
 * @author  The scaffold-interface created at 2017-07-31 06:31:48pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class Procedimentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('procedimentos',function (Blueprint $table){

            $table->increments('id');
            
            $table->string('cod_procedimento');
            
            $table->string('nome_procedimento')->unique();
            
            $table->boolean('especialista')->default(0);
            
            $table->integer('intervalo_usos')
                  ->comment('É a definição da quantidade de dias necessários para que o mesmo procedimento seja novamente realizado');

            $table->double('valor_base', 15, 4)
                  ->comment('Valor utilizado como base pra o desconto por plano');

            /**
             * Foreignkeys section
             */
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('procedimentos');
    }
}
