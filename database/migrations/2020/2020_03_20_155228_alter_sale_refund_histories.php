<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSaleRefundHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table
                ->integer("refund_value")
                ->unsigned()
                ->default(0)
                ->after("gateway_response");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table->dropColumn("refund_value");
        });
    }
}
