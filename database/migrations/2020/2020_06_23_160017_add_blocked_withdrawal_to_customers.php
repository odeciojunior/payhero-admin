<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockedWithdrawalToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customers", function (Blueprint $table) {
            $table
                ->boolean("blocked_withdrawal")
                ->default(0)
                ->after("balance");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("customers", function (Blueprint $table) {
            $table->dropColumn("blocked_withdrawal");
        });
    }
}
