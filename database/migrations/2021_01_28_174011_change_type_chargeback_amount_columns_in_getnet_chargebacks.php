<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeChargebackAmountColumnsInGetnetChargebacks extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('getnet_chargebacks', function (Blueprint $table) {
            $table->renameColumn('chargeback_amount', 'amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('getnet_chargebacks', function (Blueprint $table) {
            $table->renameColumn('amount', 'chargeback_amount');
        });
    }

}
