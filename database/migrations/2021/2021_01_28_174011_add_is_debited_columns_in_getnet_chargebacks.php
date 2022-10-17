<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDebitedColumnsInGetnetChargebacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table
                ->boolean("is_debited")
                ->default(false)
                ->after("chargeback_amount");
            $table
                ->date("debited_at")
                ->nullable()
                ->after("is_debited");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table->dropColumn(["is_debited", "debited_at"]);
        });
    }
}
