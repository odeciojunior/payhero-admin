<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchievementUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("achievement_user", function (Blueprint $table) {
            $table->unsignedBigInteger("achievement_id");
            $table->unsignedInteger("user_id");
            $table->primary(["achievement_id", "user_id"]);
            $table->timestamps();
        });

        Schema::table("achievement_user", function (Blueprint $table) {
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table
                ->foreign("achievement_id")
                ->references("id")
                ->on("achievements");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("achievement_user");
    }
}
