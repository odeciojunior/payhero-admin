<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleWoocommerceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("sale_woocommerce_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("sale_id")->nullable();
            $table->bigInteger("project_id")->nullable();
            $table->bigInteger("order")->nullable();
            $table->string("method")->nullable();
            $table->tinyInteger("status")->default(0);

            $table->json("send_data")->nullable();
            $table->text("received_data")->nullable();

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
        Schema::dropIfExists("sale_woocommerce_requests");
    }
}
