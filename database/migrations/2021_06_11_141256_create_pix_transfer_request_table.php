<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePixTransferRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("pix_transfer_requests", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("company_id")->nullable();
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table->unsignedBigInteger("withdrawal_id")->nullable();
            $table
                ->foreign("withdrawal_id")
                ->references("id")
                ->on("withdrawals");
            $table->string("pix_key");
            $table->integer("value");
            $table->json("request")->nullable();
            $table->json("response")->nullable();
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
        Schema::dropIfExists("pix_transfer_requests");
    }
}
