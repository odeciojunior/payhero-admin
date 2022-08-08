<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionToChargebacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("chargebacks", function (Blueprint $table) {
            $table
                ->json("transaction")
                ->nullable()
                ->after("status_enum");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("chargebacks", function (Blueprint $table) {
            $table->dropColumn("transaction");
        });
    }
}
