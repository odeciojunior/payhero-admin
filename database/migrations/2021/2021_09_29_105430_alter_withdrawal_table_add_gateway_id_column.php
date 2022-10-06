<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWithdrawalTableAddGatewayIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("withdrawals", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->after("company_id");
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
            $table
                ->string("gateway_transfer_id")
                ->nullable()
                ->after("gateway_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("withdrawals", function (Blueprint $table) {
            $table->dropForeign(["gateway_id"]);
            $table->dropColumn("gateway_id");
            $table->dropColumn("gateway_transfer_id");
        });
    }
}
