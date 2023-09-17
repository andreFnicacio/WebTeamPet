<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nps', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedTinyInteger('nota');
            $table->text('comentario')->nullable();
            $table->string('numero_ticket')->nullable();
            $table->enum('origem', ['web', 'mobile', 'zendesk', 'email', 'whatsapp', 'ticket']);

            $table->bigInteger('id_cliente')->unsigned()->nullable();
            $table->foreign('id_cliente')->references('id')->on('clientes');

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
        Schema::drop('nps');
    }
}
