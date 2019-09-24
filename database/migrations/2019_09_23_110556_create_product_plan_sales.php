<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPlanSales extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('product_plan_sales', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('plan_id')->index();
            $table->unsignedBigInteger('sale_id')->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('guarantee')->nullable();
            $table->string('format')->nullable();
            $table->string('cost')->nullable();
            $table->string('photo')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('weight')->nullable();
            $table->string('shopify')->nullable();
            $table->string('digital_product_url')->nullable();
            $table->string('price')->nullable();
            $table->string('shopify_id')->nullable();
            $table->string('shopify_variant')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('product_plan_sales', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
        });

        Schema::table('product_plan_sales', function(Blueprint $table) {
            $table->foreign('plan_id')->references('id')->on('plans');
        });

        Schema::table('product_plan_sales', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_plan_sales');
    }
}
