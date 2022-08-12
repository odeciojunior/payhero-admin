<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGetnetChargebacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("getnet_chargebacks", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("getnet_chargeback_detail_id")->index();
            $table->unsignedBigInteger("sale_id")->index();
            $table->unsignedInteger("company_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->unsignedInteger("user_id")->index();
            $table->date("transaction_date")->nullable();
            $table->date("installment_date")->nullable();
            $table->date("adjustment_date")->nullable();
            $table->decimal("chargeback_amount", 8, 2)->nullable();
            $table->json("body")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
            $table
                ->foreign("getnet_chargeback_detail_id")
                ->references("id")
                ->on("getnet_chargeback_details");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("getnet_chargebacks");
    }
}
