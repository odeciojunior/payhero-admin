<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTableSaleRefundHistories extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->after("sale_id");
            $table
                ->text("refund_observation")
                ->nullable()
                ->after("refund_value");
        });
        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table->dropForeign(["user_id"]);
            $table->dropColumn(["refund_observation", "user_id"]);
        });
    }
}
