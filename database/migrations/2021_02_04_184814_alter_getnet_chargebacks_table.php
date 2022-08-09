<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGetnetChargebacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table
                ->integer("tax")
                ->after("is_debited")
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table->dropColumn("tax");
        });
    }
}
