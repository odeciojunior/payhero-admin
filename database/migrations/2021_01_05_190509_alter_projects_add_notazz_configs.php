<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProjectsAddNotazzConfigs extends Migration
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
                ->json("notazz_configs")
                ->nullable()
                ->after("reviews_config_icon_color");
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
            $table->dropColumn("notazz_configs");
        });
    }
}
