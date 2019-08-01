<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlanSaleProducts extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('plan_sale_products', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->string('cost');
            $table->string('price');
            $table->timestamps();
        });

        Schema::table('plan_sale_products', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('plan_sale_products', function(Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('plan_sale_products');
    }
}
