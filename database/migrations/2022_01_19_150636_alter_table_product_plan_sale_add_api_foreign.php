<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductPlanSaleAddApiForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("products_plans_sales", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("products_sales_api_id")
                ->nullable()
                ->after("plan_id");
            $table
                ->foreign("products_sales_api_id")
                ->references("id")
                ->on("products_sales_api");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
