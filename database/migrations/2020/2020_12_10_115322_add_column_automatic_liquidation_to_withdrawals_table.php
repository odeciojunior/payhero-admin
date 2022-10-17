<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAutomaticLiquidationToWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("withdrawals", function (Blueprint $table) {
            $table->boolean("automatic_liquidation")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("withdrawals", function (Blueprint $table) {
            $table->dropColumn(["automatic_liquidation"]);
        });
    }
}
