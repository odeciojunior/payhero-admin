<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnObservationTableCustomerWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_withdrawals', function (Blueprint $table) {
            $table->text('observation')->nullable()->after('bank_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_withdrawals', function (Blueprint $table) {
            $table->dropColumn(['observation']);
        });
    }
}
