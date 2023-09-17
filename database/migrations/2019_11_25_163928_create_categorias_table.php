<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('categorias')->insert(['nome' => 'Consultório']);
        DB::table('categorias')->insert(['nome' => 'Clínica']);
        DB::table('categorias')->insert(['nome' => 'Hospital']);
        DB::table('categorias')->insert(['nome' => 'Diagnósticos de Imagem']);
        DB::table('categorias')->insert(['nome' => 'Diagnósticos Laboratoriais']);
        DB::table('categorias')->insert(['nome' => 'Reabilitação']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('categorias');
    }
}
