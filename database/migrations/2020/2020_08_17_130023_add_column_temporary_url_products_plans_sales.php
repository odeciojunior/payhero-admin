<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTemporaryUrlProductsPlansSales extends Migration
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
                ->text("temporary_url")
                ->nullable()
                ->after("digital_product_url");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("products_plans_sales", function (Blueprint $table) {
            $table->dropColumn("temporary_url");
        });
    }
}
