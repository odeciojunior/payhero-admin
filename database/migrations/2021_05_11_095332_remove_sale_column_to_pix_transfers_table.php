<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSaleColumnToPixTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pix_transfers", function (Blueprint $table) {
            $table->dropForeign("pix_transfers_sale_id_foreign");
            $table->dropColumn("sale_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("pix_transfers", function (Blueprint $table) {
            $table->unsignedBigInteger("sale_id")->nullable();
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
        });
    }
}
