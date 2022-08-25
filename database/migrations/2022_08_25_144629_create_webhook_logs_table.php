<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("webhook_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->integer("user_id")
                ->unsigned()
                ->index();
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table
                ->integer("company_id")
                ->unsigned()
                ->index();
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table->string("url");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("webhook_logs");
    }
}
