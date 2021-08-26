<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("sale_id");
            $table->string('item_id');
            $table->string('name');
            $table->float('price');
            $table->integer('quantity');
            $table->string('product_type');
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_sales');
    }
}
