<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->text('body');
            $table->string('to');
            $table->string('from');
            $table->json('attachments')->nullable();
            $table->string('hash')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->unsignedBigInteger('emailable_id')->nullable();
            $table->string('emailable_type')->nullable();

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
        Schema::drop('emails');
    }
}
