<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingDebtWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("pending_debt_withdrawals", function (Blueprint $table) {
            $table->unsignedBigInteger("pending_debt_id")->nullable();
            $table
                ->foreign("pending_debt_id")
                ->references("id")
                ->on("pending_debts");

            $table->unsignedBigInteger("withdrawal_id")->nullable();
            $table
                ->foreign("withdrawal_id")
                ->references("id")
                ->on("withdrawals");

            $table->primary(["pending_debt_id", "withdrawal_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("pending_debt_withdrawals");
    }
}
