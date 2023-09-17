<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFichasAvaliacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fichas_avaliacoes', function(Blueprint $table) {
            $table->string('data_assinatura_cliente')->nullable()->after('assinatura_cliente');
            $table->enum('meio_assinatura_cliente', [1, 2, 3])
                ->nullable()
                ->comment('Meio onde o cliente assinou a guia. 1=Sistema, 2=Aplicativo, 3=Presencial')
                ->after('data_assinatura_cliente');
            $table->string('data_assinatura_prestador')->nullable()->after('assinatura_prestador');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fichas_avaliacoes', function(Blueprint $table) {
            $table->dropColumn('data_assinatura_cliente');
            $table->dropColumn('meio_assinatura_cliente');
            $table->dropColumn('data_assinatura_prestador');
        });
    }
}
