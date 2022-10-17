<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePixTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("pix_transfers", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->nullable();
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table->unsignedBigInteger("withdrawal_id")->nullable();
            $table
                ->foreign("withdrawal_id")
                ->references("id")
                ->on("withdrawals");
            $table->string("pix_transaction_id");
            $table->integer("value");
            $table->dateTime("requested_in");
            $table->dateTime("latest_status_updated");
            $table->string("transaction_ids")->nullable();
            $table->enum("status", ["PROCESSING", "REALIZED", "UNREALIZED"]);
            $table->json("postback")->nullable();
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
        Schema::dropIfExists("pix_transfers");
    }
}
