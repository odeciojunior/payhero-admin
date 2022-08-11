<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("project_reviews", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table->json("apply_on_plans")->nullable();
            $table->string("photo")->nullable();
            $table->string("name")->nullable();
            $table->float("stars")->default(5);
            $table->string("description", 255)->nullable();
            $table->boolean("active_flag")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("project_reviews", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
        });
    }

    /**
     * Reverse the migrations.n
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("project_reviews");
    }
}
