<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePixelConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("pixel_configs", function (Blueprint $table) {
            $table->id();
            $table->text("metatags_facebook")->nullable();
            $table->unsignedInteger("project_id")->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("pixel_configs", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("pixel_configs");
    }
}
