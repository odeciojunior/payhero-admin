<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("tasks_users", function (Blueprint $table) {
            $table->unsignedBigInteger("task_id");
            $table->unsignedInteger("user_id");
            $table->primary(["task_id", "user_id"]);
            $table->timestamps();
        });

        Schema::table("tasks_users", function (Blueprint $table) {
            $table
                ->foreign("task_id")
                ->references("id")
                ->on("tasks");
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
        Schema::dropIfExists("tasks_users");
    }
}
