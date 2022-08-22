<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnToUnderAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("under_attacks", function (Blueprint $table) {
            $table->renameColumn("percentage_card_declined", "percentage_card_refused");
            $table->renameColumn("start_date_card_declined", "start_date_card_refused");
            $table->renameColumn("end_date_card_declined", "end_date_card_refused");
            $table->renameColumn("user_card_declined", "total_refused");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("under_attacks", function (Blueprint $table) {
            $table->renameColumn("percentage_card_refused", "percentage_card_declined");
            $table->renameColumn("start_date_card_refused", "start_date_card_declined");
            $table->renameColumn("end_date_card_refused", "end_date_card_declined");
            $table->renameColumn("total_refused", "user_card_declined");
        });
    }
}
