<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConveniadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conveniadas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('nome', 200);
            $table->string('telefone', 40);
            $table->string('contato', 70);
            $table->string('email', 70)->nullable();
            $table->boolean('ativo')->default(1)->comment('0 - Inativo, 1 - Ativo');
            $table->enum('tipo_desconto',['P','F'])->default('P');
            $table->decimal('desconto',11,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conveniadas');
    }
}
