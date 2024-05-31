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
        Schema::create("utmify_integrations", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("project_id")->index();
            $table->unsignedInteger("user_id")->index();
            $table->string("token");
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("utmify_integrations");
    }
};
