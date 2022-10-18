<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGatewayReleasedAtToTransactions extends Migration
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
                ->timestamp("gateway_released_at")
                ->nullable()
                ->after("invitation_id");
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
            $table->dropColumn("gateway_released_at");
        });
    }
}
