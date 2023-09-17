<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConveniados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('conveniados', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nome_conveniado', 255);
            $table->string('contato_principal')
                  ->comment('Pessoa para contactar dentro da instituição');
            $table->string('email_contato');
            $table->string('cep');
            $table->string('rua');
            $table->string('numero_endereco');
            $table->string('complemento_endereco')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado');
            $table->float('desconto_porcentagem', 3, 2)->default(0.00);
            $table->string('telefone')->default(0.00, 3, 2);
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
        Schema::drop('conveniados');
    }
}
