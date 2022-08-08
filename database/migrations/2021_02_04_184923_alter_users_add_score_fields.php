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
        Schema::table("users", function (Blueprint $table) {
            $table
                ->float("chargeback_rate")
                ->after("account_is_approved")
                ->nullable();
            $table
                ->float("account_score")
                ->after("chargeback_rate")
                ->nullable();
            $table
                ->float("chargeback_score")
                ->after("account_score")
                ->nullable();
            $table
                ->float("attendance_score")
                ->after("chargeback_score")
                ->nullable();
            $table
                ->float("tracking_score")
                ->after("attendance_score")
                ->nullable();
            $table
                ->float("attendance_average_response_time")
                ->after("tracking_score")
                ->nullable();
            $table
                ->float("installment_cashback")
                ->after("attendance_average_response_time")
                ->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("chargeback_rate");
            $table->dropColumn("account_score");
            $table->dropColumn("chargeback_score");
            $table->dropColumn("attendance_score");
            $table->dropColumn("tracking_score");
            $table->dropColumn("attendance_average_response_time");
            $table->dropColumn("installment_cashback");
        });
    }
}
