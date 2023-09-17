<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsAddAngel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets', function(Blueprint $table) {
            $table->boolean('angel')->default(0);
            $table->decimal('valor_angel')->nullable();
            $table->dateTime('data_angel')->nullable();
            $table->smallInteger('carencia_angel')->nullable();
            $table->enum('regime_angel', ["MENSAL", "ANUAL"])->nullable();
            $table->enum('meio_angel', [1, 2, 3])
                ->nullable()
                ->comment('Meio onde o cliente aderiu ao angel. 1=Sistema, 2=Aplicativo, 3=Site');
            $table->boolean('obito')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pets', function(Blueprint $table) {
            $table->dropColumn('angel');
            $table->dropColumn('valor_angel');
            $table->dropColumn('data_angel');
            $table->dropColumn('carencia_angel');
            $table->dropColumn('regime_angel');
            $table->dropColumn('meio_angel');
            $table->dropColumn('obito');
        });
    }
}
