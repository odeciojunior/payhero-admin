<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDebtPendingValueToWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("withdrawals", function (Blueprint $table) {
            $table
                ->integer("debt_pending_value")
                ->nullable()
                ->after("is_released");
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
            $table->dropColumn(["debt_pending_value"]);
        });
    }
}
