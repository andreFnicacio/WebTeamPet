<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRdStationEnviosTable extends Migration
{
    /**
     * Run the migrations...
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rd_station_envios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tabela', 200);
            $table->integer('tabela_id')->unsigned();
            $table->string('identificador', 200);
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
        Schema::dropIfExists('rd_station_envios');
    }
}
