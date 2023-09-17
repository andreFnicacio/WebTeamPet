<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class Planos_grupos.
 *
 * @author  The scaffold-interface created at 2017-07-31 06:57:32pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class PlanosGrupos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('planos_grupos',function (Blueprint $table){

            $table->increments('id');
            
            $table->boolean('liberacao_automatica')
                  ->comment('Marca se aquele plano com aquele grupo tem liberação automática nos procedimentos');
            
            $table->integer('dias_carencia')
                  ->comment('Pode ser que um grupo tenha carência diferente dependendo do plano. Vide castração no Platinum e Fidelidade');
            
            $table->integer('quantidade_usos')
                  ->comment('Quantidade de usos permitidas naquele plano, naquele grupo.');

            $table->float('valor_desconto',  3, 2)
                    ->comment('Porcentagem a ser descontada dos procedimentos dos grupos para o plano determinado');

            $table->integer('plano_id')
                    ->unsigned();

            $table->integer('grupo_id')
                  ->unsigned();
            
            
            $table->timestamps();

            // type your addition here
            $table->foreign('plano_id')->references('id')->on('planos')->onDelete('cascade');
            $table->foreign('grupo_id')->references('id')->on('grupos_carencias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('planos_grupos');
    }
}
