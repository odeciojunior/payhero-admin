<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlockedBalanceToUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table("user_notifications", function (Blueprint $table) {
            $table
                ->boolean("blocked_balance")
                ->default(true)
                ->after("shopify");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table("user_notifications", function (Blueprint $table) {
            $table->dropColumn("blocked_balance");
        });
    }
}
