<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleShopifyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sale_shopify_requests', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project');
            $table->string('method');
            $table->unsignedBigInteger('sale_id');
            $table->json('send_data')->nullable();
            $table->json('received_data')->nullable();
            $table->json('exceptions')->nullable();
            $table->timestamps();
        });
        Schema::table('sale_shopify_requests', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_shopify_requests', function(Blueprint $table) {
            $table->dropForeign(["sale_id"]);
        });
        Schema::dropIfExists('sale_shopify_requests');
    }
}
