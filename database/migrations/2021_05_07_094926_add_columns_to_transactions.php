<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transactions", function (Blueprint $table) {
            $table
                ->boolean("tracking_required")
                ->default(true)
                ->after("is_waiting_withdrawal");
            $table
                ->boolean("is_security_reserve")
                ->default(false)
                ->after("tracking_required");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transactions", function (Blueprint $table) {
            $table->dropColumn(["tracking_required", "is_security_reserve"]);
        });
    }
}
