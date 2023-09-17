<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanoAngelValoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plano_angel_valores', function(Blueprint $table) {
            $table->increments('id');
            $table->decimal('valor_mensal', 10, 2);
            $table->decimal('valor_anual', 10, 2);
            $table->integer('idade_min');
            $table->integer('idade_max')->nullable();

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
        Schema::drop('plano_angel_valores');
    }
}
