<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("webhooks", function (Blueprint $table) {
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
            $table->string("description");
            $table->string("url");
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
        Schema::dropIfExists("webhooks");
    }
}
