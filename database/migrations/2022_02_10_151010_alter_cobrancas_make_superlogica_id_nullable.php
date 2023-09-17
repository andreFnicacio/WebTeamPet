<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCobrancasMakeSuperlogicaIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            DB::statement('ALTER TABLE `cobrancas` MODIFY `old_superlogica_id` VARCHAR(191) NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            DB::statement('ALTER TABLE `cobrancas` MODIFY `old_superlogica_id` VARCHAR(191) NOT NULL;');
        });
    }
}
