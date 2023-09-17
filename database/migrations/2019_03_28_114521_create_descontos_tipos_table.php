<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescontosTiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descontos_tipos', function(Blueprint $table) {
            $table->increments('id');

            $table->string('tipo_desconto');
            $table->string('nome_desconto');
            $table->decimal('valor_desconto', 5, 2);

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
        Schema::drop('descontos_tipos');
    }
}