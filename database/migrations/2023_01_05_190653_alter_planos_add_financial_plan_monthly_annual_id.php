<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanosAddFinancialPlanMonthlyAnnualId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->integer('financial_plan_monthly_id')->nullable()->comment('Financial Service Plan ID for monthly fee');
            $table->integer('financial_plan_annual_id')->nullable()->comment('Financial Service Plan ID for annual fee');
        });
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
