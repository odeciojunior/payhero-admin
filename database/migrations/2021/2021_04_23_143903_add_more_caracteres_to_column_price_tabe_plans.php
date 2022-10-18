<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreCaracteresToColumnPriceTabePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("plans", function (Blueprint $table) {
            $table->decimal("price", 30, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("plans", function (Blueprint $table) {
            $table->decimal("price", 8, 2)->change();
        });
    }
}
