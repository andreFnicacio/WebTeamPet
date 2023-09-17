<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pix', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_id');
            $table->string('status');
            $table->string('description');
            $table->string('transaction_id');
            $table->text('qr_code')->nullable();
            $table->dateTime('creation_date_qrcode');
            $table->dateTime('expiration_date_qrcode');
            $table->string('psp_code');

            $table->string('callback_url')->nullable();
            $table->string('local_description');

            $table->bigInteger('id_cliente')->unsigned();

            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('cascade');

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
        Schema::dropIfExists('pix');
    }
}
