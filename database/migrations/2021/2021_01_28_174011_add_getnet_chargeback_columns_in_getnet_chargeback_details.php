<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGetnetChargebackColumnsInGetnetChargebackDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("getnet_chargeback_details", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("getnet_chargeback_id")
                ->nullable()
                ->index();
            $table
                ->foreign("getnet_chargeback_id")
                ->references("id")
                ->on("getnet_chargebacks");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("getnet_chargeback_details", function (Blueprint $table) {
            $table->dropColumn("getnet_chargeback_id");
        });
    }
}
