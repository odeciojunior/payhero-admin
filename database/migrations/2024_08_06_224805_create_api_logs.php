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
        Schema::create("api_logs", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("sale_id")->nullable();
            $table->json("request")->nullable();
            $table->json("response")->nullable();
            $table->json("error")->nullable();
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
        Schema::dropIfExists("api_logs");
    }
};
