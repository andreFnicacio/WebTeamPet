<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocoes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('nome', 200);
            $table->date('dt_inicio');
            $table->date('dt_termino')->nullable();
            $table->boolean('cumulativo')->default(0)->comment('0 - Não, 1 - Sim');
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
        Schema::dropIfExists('promocoes');
    }
}
