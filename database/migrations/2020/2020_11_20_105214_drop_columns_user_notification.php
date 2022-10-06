<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsUserNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("user_notifications", function (Blueprint $table) {
            $table->dropColumn("released_balance");
            $table->dropColumn("credit_card_in_proccess");
            $table->dropColumn("blocked_balance");
            $table->dropColumn("notazz");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("user_notifications", function (Blueprint $table) {
            $table->tinyInteger("released_balance")->default(1);
            $table->tinyInteger("credit_card_in_proccess")->default(1);
            $table->tinyInteger("blocked_balance")->default(1);
            $table->tinyInteger("notazz")->default(1);
        });
    }
}
