<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnRequestToPixTransferRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pix_transfer_requests", function (Blueprint $table) {
            $table->text("request")->change();
            $table->text("response")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table->dropColumn("status");
        });
    }
}
