<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveGetnetDetailColumnsInGetnetChargebacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table->dropForeign(["getnet_chargeback_detail_id"]);
            $table->dropColumn("getnet_chargeback_detail_id");
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
            $table->unsignedBigInteger("getnet_chargeback_detail_id")->index();
        });
    }
}
