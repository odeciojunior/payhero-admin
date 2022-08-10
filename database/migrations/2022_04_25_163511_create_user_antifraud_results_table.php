<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAntifraudResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("user_antifraud_results", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table->string("action")->index();
            $table->foreignId("antifraud_id")->constrained("antifrauds");
            $table->json("send_data");
            $table->json("antifraud_result")->nullable();
            $table->string("status")->index();
            $table->json("translated_codes")->nullable();
            $table->json("antifraud_exceptions")->nullable();
            $table->timestamps();
        });

        Schema::table("user_antifraud_results", function (Blueprint $table) {
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
        Schema::dropIfExists("user_antifraud_results");
    }
}
