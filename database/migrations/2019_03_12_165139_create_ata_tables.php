<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atas', function(Blueprint $table) {
            $table->increments('id')->comment('Sequência única de criação da ata');
            $table->integer('autor')->unsigned();
            $table->dateTime('published_at');
            $table->timestamps();
            $table->integer('originaria')->nullable()->comment('Indica se uma ata originou a atual como correção');
            $table->string('titulo');
            $table->text('corpo');
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('atas');
    }
}
