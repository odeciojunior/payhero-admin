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
        Schema::create("customer_bureau_results", function (Blueprint $table) {
            $table->id();
            $table->foreignId("customer_id")->constrained();
            $table->string("vendor", 20);
            $table->json("send_data")->nullable();
            $table->json("result_data")->nullable();
            $table->json("exception")->nullable();
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
        Schema::dropIfExists("customer_bureau_results");
    }
};
