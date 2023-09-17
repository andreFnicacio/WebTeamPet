<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrhHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urh_historico', function(Blueprint $table) {
            $table->increments('id');
            $table->decimal('valor_urh', 5, 2)->unsigned();

            $table->integer('id_urh')->unsigned();
            $table->foreign('id_urh')->references('id')->on('urh');

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
        Schema::drop('urh_historico');
    }
}
