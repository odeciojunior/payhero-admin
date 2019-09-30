<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProductPlanSaleIdTableTrackingHistories extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_histories', function($table) {
            $table->unsignedBigInteger('product_plan_sale_id')->nullable()->index()->after('delivery_id');
        });

        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->foreign('product_plan_sale_id')->references('id')->on('products_plans_sales');
        });

        $sql = 'UPDATE tracking_histories SET delivery_id = null';
        DB::select($sql);

        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->dropForeign(['delivery_id']);
            $table->dropColumn('delivery_id');
            $table->dropColumn('plans_sale_id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_histories', function($table) {
            $table->dropForeign(['product_plan_sale_id']);
        });

        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->unsignedBigInteger('delivery_id')->nullable()->index();
            $table->unsignedBigInteger('plans_sale_id')->nullable()->index();
        });

        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->foreign('delivery_id')->references('id')->on('deliveries');
        });
    }

}
