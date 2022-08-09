<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewsConfigToProjectTable extends Migration
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
                ->string("reviews_config_icon_type", 20)
                ->default("star")
                ->after("countdown_timer_finished_message");
            $table
                ->string("reviews_config_icon_color", 7)
                ->default("#f8ce1c")
                ->after("reviews_config_icon_type");
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
            $table->dropColumn("reviews_config_icon_type");
            $table->dropColumn("reviews_config_icon_color");
        });
    }
}
