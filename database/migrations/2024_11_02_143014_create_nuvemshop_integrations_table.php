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
        Schema::create("nuvemshop_integrations", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("project_id")->nullable();
            $table->string("url_store");
            $table->string("token")->nullable();
            $table->string("store_id")->nullable();
            $table->enum("status", ["PENDING", "ACTIVE", "INACTIVE"])->default("PENDING");
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
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
        Schema::dropIfExists("nuvemshop_integrations");
    }
};
