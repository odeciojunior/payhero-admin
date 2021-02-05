<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAddScoreFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('chargeback_tax')->after('account_is_approved')->nullable();
            $table->integer('account_score')->after('account_is_approved')->nullable();
            $table->integer('chargeback_score')->after('account_is_approved')->nullable();
            $table->integer('attendance_score')->after('account_is_approved')->nullable();
            $table->integer('tracking_score')->after('account_is_approved')->nullable();
            $table->integer('attendance_average_response_time')->after('account_is_approved')->nullable();
            $table->integer('installment_cashback')->after('account_is_approved')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('chargeback_tax');
            $table->dropColumn('account_score');
            $table->dropColumn('chargeback_score');
            $table->dropColumn('attendance_score');
            $table->dropColumn('tracking_score');
            $table->dropColumn('attendance_average_response_time');
            $table->dropColumn('installment_cashback');
            
        });
    }
}
