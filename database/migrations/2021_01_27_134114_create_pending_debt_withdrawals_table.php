<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingDebitWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_debit_withdrawals', function (Blueprint $table) {
            $table->unsignedBigInteger("pending_debit_id")->nullable();
            $table->foreign('pending_debit_id')->references('id')->on('pending_debts');

            $table->unsignedBigInteger("withdrawal_id")->nullable();
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals');

            $table->primary(['pending_debit_id', 'withdrawal_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_debit_withdrawals');
    }
}
