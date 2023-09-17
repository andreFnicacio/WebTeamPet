<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDespesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despesas', function(Blueprint $table) {
            $table->renameColumn('id_centrocusto', 'id_centrocusto_superlogica');
            $table->renameColumn('descricao', 'nome_centrocusto');
            $table->renameColumn('valor', 'valor_participacao');
            $table->integer('id_superlogica')->unsigned();
            $table->string('forma_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despesas', function(Blueprint $table) {
            $table->renameColumn('id_centrocusto_superlogica', 'id_centrocusto');
            $table->renameColumn('nome_centrocusto', 'descricao');
            $table->renameColumn('valor_participacao', 'valor');
            $table->dropColumn('id_superlogica');
            $table->dropColumn('forma_pagamento');
        });
    }
}
