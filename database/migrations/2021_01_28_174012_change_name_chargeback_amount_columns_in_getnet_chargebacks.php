<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameChargebackAmountColumnsInGetnetChargebacks extends Migration
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
                ->integer("amount")
                ->nullable()
                ->change();
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
            $table
                ->decimal("amount", 8, 2)
                ->nullable()
                ->change();
        });
    }
}
