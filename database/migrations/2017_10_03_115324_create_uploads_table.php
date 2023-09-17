<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description')->nullable();
            $table->string('original_name');
            $table->string('mime');
            $table->string('extension')->nullable();
            $table->string('path');
            $table->double('size');
            $table->string('bind_with')->nullable()
                ->comment('Campo que indica que tabela está relacionada com o campo');
            $table->integer('binded_id')->nullable();
            $table->integer('user_id')->unsigned();
            $table->boolean('public')->default(0);

            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::drop('uploads');
    }
}
