<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewaysBackofficeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("gateways_backoffice_requests", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("company_id");
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table->unsignedBigInteger("gateway_id");
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
            $table->json("sent_data")->nullable();
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
        Schema::dropIfExists("gateways_backoffice_requests");
    }
}
