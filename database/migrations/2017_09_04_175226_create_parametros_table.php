<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('parametros')) {
            Schema::create('parametros', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('chave');
                $table->string('valor');
                $table->string('tipo');
                $table->text('descricao')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('parametros');
    }
}
