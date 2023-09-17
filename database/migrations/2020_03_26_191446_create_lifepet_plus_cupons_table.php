<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifepetPlusCuponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifepet_plus_cupons', function(Blueprint $table) {
            $table->increments('id');
            $table->string('codigo');
            $table->string('descricao')->nullable();
            $table->decimal('valor', 10, 2);
            $table->datetime('data_inicio');
            $table->datetime('data_validade')->nullable();
           
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
        Schema::drop('lifepet_plus_cupons');
    }
}