<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientesFarmapet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function(Blueprint $table) {
            $table->boolean('farmapet')->default(0);
            $table->decimal('valor_farmapet')->nullable();
            $table->decimal('saldo_farmapet', 13, 2)->nullable();
            $table->dateTime('data_farmapet')->nullable();
            $table->smallInteger('carencia_farmapet')->nullable();
            $table->enum('regime_farmapet', ["MENSAL", "ANUAL"])->nullable();
            $table->enum('meio_farmapet', [1, 2, 3])
                ->nullable()
                ->comment('Meio onde o cliente aderiu ao Farmapet. 1=Sistema, 2=Aplicativo, 3=Site');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function(Blueprint $table) {
            $table->dropColumn('farmapet');
            $table->dropColumn('valor_farmapet');
            $table->dropColumn('saldo_farmapet');
            $table->dropColumn('data_farmapet');
            $table->dropColumn('carencia_farmapet');
            $table->dropColumn('regime_farmapet');
            $table->dropColumn('meio_farmapet');
        });
    }
}
