<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pushes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('message');
            $table->integer('count');
            $table->string('status')->default('OPEN');
            $table->integer('progress')->default(0);
            $table->longText('meta')->comment('Grava os dados de clientes serializados.');
            $table->integer('author')->unsigned();

            $table->timestamps();

            $table->foreign('author')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pushes');
    }
}
