<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExpirationToSaleContestationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table
                ->date("expiration_date")
                ->nullable()
                ->after("request_date");
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
            $table->dropColumn("expiration_date");
        });
    }
}
