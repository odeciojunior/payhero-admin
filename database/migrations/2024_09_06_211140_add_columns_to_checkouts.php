<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("checkouts", function (Blueprint $table) {
            $table
                ->string("original_url")
                ->nullable()
                ->default(null)
                ->after("referer");

            $table
                ->json("pixel_data")
                ->nullable()
                ->default(null)
                ->after("original_url");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("checkouts", function (Blueprint $table) {
            $table->dropColumn("original_url");
            $table->dropColumn("pixel_data");
        });
    }
};
