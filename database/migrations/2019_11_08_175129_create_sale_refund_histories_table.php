<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleRefundHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sale_refund_histories', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->integer('refunded_amount');
            $table->timestamp('date_refunded');
            $table->json('gateway_response');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('sale_refund_histories', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_refund_histories');
    }
}
