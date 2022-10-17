<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCheckoutNotificationsConfigsToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table
                ->json("checkout_notification_configs")
                ->nullable()
                ->after("finalizing_purchase_configs");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->dropColumn(["checkout_notification_configs"]);
        });
    }
}
