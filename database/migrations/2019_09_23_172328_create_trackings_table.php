<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('trackings', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_plan_sale_id')->nullable()->index();
            $table->unsignedBigInteger('plans_sale_id')->nullable()->index();
            $table->unsignedBigInteger('delivery_id')->nullable()->index();
            $table->string('tracking_code');
            $table->tinyInteger('tracking_type_enum');
            $table->tinyInteger('tracking_status_enum');
            $table->dateTime('tracking_date');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('trackings', function(Blueprint $table) {
            $table->foreign('product_plan_sale_id')->references('id')->on('products_plans_sales');
        });

        Schema::table('trackings', function(Blueprint $table) {
            $table->foreign('plans_sale_id')->references('id')->on('plans_sales');
        });

        Schema::table('trackings', function(Blueprint $table) {
            $table->foreign('delivery_id')->references('id')->on('deliveries');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trackings');
    }
}
