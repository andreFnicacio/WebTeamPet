<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urh', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome_urh');
            $table->decimal('valor_urh', 5, 2)->unsigned();
            $table->dateTime('data_validade');
            $table->boolean('ativo')->default(1);

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
        Schema::drop('urh');
    }
}
