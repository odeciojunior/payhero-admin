<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSystemStatusEnumToTrackings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("trackings", function (Blueprint $table) {
            $table
                ->tinyInteger("system_status_enum")
                ->after("tracking_status_enum")
                ->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("trackings", function (Blueprint $table) {
            $table->dropColumn("system_status_enum");
        });
    }
}
