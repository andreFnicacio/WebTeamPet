<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaixasPlanosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faixas_planos', function(Blueprint $table) {
            $table->increments('id');
            $table->string('descricao');
            $table->decimal('valor')
                  ->comment("Valor em porcentagem que será acrescido/descontado do procedimento")
                  ->default(0);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('faixas_planos');
    }
}
