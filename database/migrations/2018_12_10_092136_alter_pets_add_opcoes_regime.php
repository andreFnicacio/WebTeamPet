<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPetsAddOpcoesRegime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $options = join(',', [
            "'ANUAL'",
            "'MENSAL'",
            "'ANUAL EM 2X'",
            "'ANUAL EM 3X'",
            "'ANUAL EM 4X'",
            "'ANUAL EM 5X'",
            "'ANUAL EM 6X'",
            "'ANUAL EM 7X'",
            "'ANUAL EM 8X'",
            "'ANUAL EM 9X'",
            "'ANUAL EM 10X'",
            "'ANUAL EM 11X'",
            "'ANUAL EM 12X'",
        ]);

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE pets CHANGE COLUMN regime regime ENUM($options) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
