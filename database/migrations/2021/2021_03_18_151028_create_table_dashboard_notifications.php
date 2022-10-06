<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDashboardNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists("dashboard_notifications");

        Schema::create("dashboard_notifications", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->integer("user_id")
                ->unsigned()
                ->index();
            $table->bigInteger("subject_id");
            $table->string("subject_type");
            $table->timestamp("read_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("dashboard_notifications", function (Blueprint $table) {
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("dashboard_notifications");
    }
}
