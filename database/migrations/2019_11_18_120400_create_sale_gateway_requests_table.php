<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleGatewayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sale_gateway_requests', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->json('send_data')->nullable();
            $table->json('gateway_result')->nullable();
            $table->json('gateway_exceptions')->nullable();
            $table->timestamps();
        });
        Schema::table('sale_gateway_requests', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_antifraud_results', function(Blueprint $table) {
            $table->dropForeign(["sale_id"]);
            $table->dropForeign(["gateway_id"]);
        });
        Schema::dropIfExists('sale_gateway_requests');
    }
}
