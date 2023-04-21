<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->integer("credit_card_release_money_days")->after("gateway_release_money_days");
            $table->integer("bank_slip_release_money_days")->after("credit_card_release_money_days");
            $table->integer("pix_release_money_days")->after("bank_slip_release_money_days");
            $table->dropColumn("gateway_release_money_days");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn("credit_card_release_money_days");
            $table->dropColumn("bank_slip_release_money_days");
            $table->dropColumn("pix_release_money_days");
            $table->integer("gateway_release_money_days");
        });
    }
};
