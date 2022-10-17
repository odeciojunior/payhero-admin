<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnChargebackRecoveredFlagOnSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->boolean("is_chargeback_recovered")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->dropColumn("is_chargeback_recovered");
        });
    }
}
