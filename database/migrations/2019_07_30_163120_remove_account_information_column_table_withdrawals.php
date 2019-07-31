<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAccountInformationColumnTableWithdrawals extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals', function(Blueprint $table) {
            $table->dropColumn('account_information');
            $table->string('bank')->nullable();
            $table->string('agency')->nullable();
            $table->string('agency_digit')->nullable();
            $table->string('account')->nullable();
            $table->string('account_digit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawals', function(Blueprint $table) {
            $table->string('account_information');
            $table->dropColumn('bank');
            $table->dropColumn('agency');
            $table->dropColumn('agency_digit');
            $table->dropColumn('account');
            $table->dropColumn('account_digit');
        });
    }
}
