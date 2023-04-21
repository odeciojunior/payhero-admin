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
        Schema::drop("transfeera_requests");

        Schema::create("transfer_gateway_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->foreign("withdrawal_id")
                ->references("id")
                ->on("withdrawals");
            $table
                ->unsignedInteger("gateway_id")
                ->nullable()
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");

            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
        });

        Schema::drop("transfeera_postbacks");

        Schema::create("transfer_gateway_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->foreign("company_id")
                ->references("id")
                ->on("companies");

            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->foreign("withdrawal_id")
                ->references("id")
                ->on("withdrawals");
            $table->json("data");
            $table->tinyInteger("processed_flag")->default(0);
            $table->json("machine_result")->nullable();
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
        Schema::dropIfExists("withdrawal_gateway_requests");
    }
};
