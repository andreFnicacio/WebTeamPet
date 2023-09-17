<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cobranca')->unsigned();
            $table->date('data_pagamento');
            $table->text('complemento')->nullable();
            $table->tinyInteger('forma_pagamento')->default(0)
                                                  ->comment('0 - Boleto, 1 - Crédito, 2 - Débito');
            $table->double('valor_pago');
            $table->integer('id_pagamento_superlogica')->nullable();
        
            //Timestamps (CREATED_AT, UPDATED_AT)
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
        Schema::drop('pagamentos');
    }
}
