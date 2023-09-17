<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function(Blueprint $table) {
            if(!Schema::hasColumn('clientes', 'dia_vencimento')) {
                $table->tinyInteger('dia_vencimento')->nullable();
            }; 

            if(!Schema::hasColumn('clientes', 'forma_pagamento')) {
                $table->enum('forma_pagamento',['boleto','cartao'])->nullable();
            }; 
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
            if(Schema::hasColumn('clientes', 'dia_vencimento')) {
                $table->dropColumn('dia_vencimento');
            }

            if(Schema::hasColumn('clientes', 'forma_pagamento')) {
                $table->dropColumn('forma_pagamento');
            }
        });
    }
}
