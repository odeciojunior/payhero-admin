<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSaleAdditionalCustomerInformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_additional_customer_informations', function (Blueprint $table) {
            $table->increments('id'); 
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('product_id');
            
            $table->enum('type_enum',['Text','File','Image'])->default('Text');
            $table->string('value',250)->nullable()->default(null);
            $table->string('file_name',100)->nullable()->default(null);
            $table->string('label',100)->nullable()->default(null);

            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('sale_additional_customer_informations');
    }
}
