<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartfunnelIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("smartfunnel_integrations", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("api_url");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("smartfunnel_integrations", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
        });

        Schema::table("smartfunnel_integrations", function (Blueprint $table) {
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
        Schema::dropIfExists("smartfunnel_integrations");
    }
}
