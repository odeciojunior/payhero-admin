<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBiometryResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("user_biometry_results", function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->unsigned();
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table->string("vendor")->index();
            $table
                ->string("biometry_id")
                ->nullable()
                ->index();
            $table->string("score")->nullable();
            $table
                ->string("status")
                ->nullable()
                ->index();
            $table->json("request_data")->nullable();
            $table->json("response_data")->nullable();
            $table->json("postback_data")->nullable();
            $table->json("api_data")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("user_biometry_results");
    }
}
