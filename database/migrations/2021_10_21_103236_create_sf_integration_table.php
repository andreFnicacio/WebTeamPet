<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfIntegrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_integration', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('id_cliente')->unsigned();
            $table->dateTime('sync_at')->nullable();
            $table->dateTime('last_sync_at')->nullable();
            $table->dateTime('error_comunication')->nullable();
            $table->dateTime('error_customer')->nullable();
            $table->dateTime('error_cpf')->nullable();
            $table->dateTime('error_subscription')->nullable();

            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_integration');
    }
}
