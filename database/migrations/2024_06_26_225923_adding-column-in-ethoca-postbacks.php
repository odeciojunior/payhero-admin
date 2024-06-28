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
        Schema::table("ethoca_postbacks", function (Blueprint $table) {
            $table
                ->enum("type", ["VISA", "MASTERCARD"])
                ->default(null)
                ->after("alert_id");
            $table
                ->enum("reply_sent", ["PENDING", "ACCOUNT_SUSPENDED", "NOTFOUND", "OTHER", "ERROR"])
                ->default("PENDING")
                ->after("machine_result");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("ethoca_postbacks", function (Blueprint $table) {
            $table->dropColumn("reply_sent");
        });
    }
};
