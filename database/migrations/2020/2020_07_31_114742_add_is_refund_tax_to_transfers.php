<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRefundTaxToTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transfers", function (Blueprint $table) {
            $table
                ->boolean("is_refund_tax")
                ->default(0)
                ->after("reason");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transfers", function (Blueprint $table) {
            $table->dropColumn("is_refund_tax");
        });
    }
}
