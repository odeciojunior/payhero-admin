<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardDeclinedToUnderAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("under_attacks", function (Blueprint $table) {
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->after("domain_id");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table
                ->enum("type", ["DOMAIN", "CARD_DECLINED"])
                ->default("DOMAIN")
                ->after("user_id");
            $table
                ->string("user_card_declined")
                ->nullable()
                ->after("type");
            $table
                ->string("percentage_card_declined")
                ->nullable()
                ->after("type");
            $table
                ->date("start_date_card_declined")
                ->nullable()
                ->after("percentage_card_declined");
            $table
                ->date("end_date_card_declined")
                ->nullable()
                ->after("start_date_card_declined");
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
            $table->dropColumn("type");
            $table->dropColumn("percentage_card_declined");
            $table->dropColumn("start_date_card_declined");
            $table->dropColumn("end_date_card_declined");
        });
    }
}
