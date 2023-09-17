<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancelamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancelamentos', function(Blueprint $table) {
            $table->increments('id');

            $table->enum('motivo', ['INADIMPLENCIA', 'INTERESSE', 'FINANCEIRO', 'DESCONTENTAMENTO', 'JURIDICO', 'OBITO', 'OUTROS']);
            $table->text('justificativa');
            $table->date('data_cancelamento')->comment('Data em que o cancelamento foi executado. O cancelamento pode ser planejado com datas futuras.');


            $table->integer('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('users');

            $table->integer('id_pet')->unsigned();
            $table->foreign('id_pet')->references('id')->on('pets');

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
        Schema::drop('cancelamentos');
    }
}
