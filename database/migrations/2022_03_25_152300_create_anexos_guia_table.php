<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnexosGuiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexos_guia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('original_name');
            $table->string('mime');
            $table->string('extension')->nullable();
            $table->string('path');
            $table->double('size');
            $table->string('numero_guia')
                ->comment('Campo que recebe o número da guia');
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('anexos_guia');
    }
}
