<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTrackingHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_histories', function(Blueprint $table){
            $table->dropForeign(['product_plan_sale_id']);
            $table->dropColumn('product_plan_sale_id');
            $table->dropColumn('tracking_type_enum');
            $table->dropColumn('tracking_date');
            $table->dropColumn('description');
            $table->unsignedBigInteger('tracking_id')->after('id');
            $table->foreign('tracking_id')->references('id')->on('trackings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('product_plan_sale_id')->after('id');
            $table->foreign('product_plan_sale_id')->references('id')->on('products_plans_sales');
            $table->integer('tracking_type_enum')->after('updated_at');
            $table->timestamp('tracking_date')->nullable()->after('tracking_status_enum');
            $table->string('description')->after('tracking_date');
            $table->dropForeign(['tracking_id']);
            $table->dropColumn('tracking_id');
        });
    }
}
