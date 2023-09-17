<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLptCodigosPromocionais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lpt__codigos_promocionais', function(Blueprint $table) {
            $table->enum('tipo_desconto', ['fixo', 'percentual'])->default('percentual');
            $table->boolean('permanente')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lpt__codigos_promocionais', function(Blueprint $table) {
            $table->dropColumn('tipo_desconto');
            $table->dropColumn('permanente');
        });
    }
}
