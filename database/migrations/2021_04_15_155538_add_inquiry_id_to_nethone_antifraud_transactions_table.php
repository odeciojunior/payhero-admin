<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInquiryIdToNethoneAntifraudTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("nethone_antifraud_transaction", function (Blueprint $table) {
            $table
                ->string("inquiry_id")
                ->after("sale_id")
                ->nullable()
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("nethone_antifraud_transaction", function (Blueprint $table) {
            $table->dropColumn("inquiry_id");
        });
    }
}
