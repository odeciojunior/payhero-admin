<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMelhorenvioIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("melhorenvio_integrations", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->string("name");
            $table->string("client_id");
            $table->string("client_secret");
            $table->text("access_token")->nullable();
            $table->text("refresh_token")->nullable();
            $table->timestamp("expiration")->nullable();
            $table->boolean("completed")->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table("melhorenvio_integrations", function (Blueprint $table) {
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
        Schema::dropIfExists("melhorenvio_integrations");
    }
}
