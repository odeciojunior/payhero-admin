<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWoocommerceIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("woo_commerce_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("url_store");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("token_user");
            $table->string("token_pass");
            $table->tinyInteger("status");
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table("woo_commerce_integrations", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
        });

        Schema::table("woo_commerce_integrations", function (Blueprint $table) {
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("woo_commerce_integrations");
    }
}
