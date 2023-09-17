<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanosProcedimentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("planos_procedimentos", function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_procedimento')->unsigned();
            $table->integer('id_plano')->unsigned();
            $table->timestamps();
            $table->text('observacao');
            $table->integer('bichos_carencia')->default(30)->comment('Carência do procedimento para o plano da bichos');
            $table->double('bichos_participacao')->default(100)->comment('Taxa de participação do plano no valor total do procedimento em %');
            $table->integer('bichos_quantidade_usos')->default(30)->comment('Quantidade de utilizações do procedimento da bichos');
            /**
             * Foreign Keys
             */

            $table->foreign('id_procedimento')->references('id')->on('procedimentos');
            $table->foreign('id_plano')->references('id')->on('planos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("planos_procedimentos");
    }
}
