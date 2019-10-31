<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTrackings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trackings', function (Blueprint $table) {

            $table->dropForeign(['plans_sale_id']);

            $table->dropColumn('plans_sale_id');
            $table->dropColumn('tracking_date');
            $table->dropColumn('description');

            $table->unsignedBigInteger('sale_id')->after('id');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->unsignedBigInteger('product_id')->after('sale_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('amount')->after('product_id');
            $table->string('tracking_code')->after('delivery_id');
            $table->integer('tracking_status_enum')->after('tracking_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trackings', function (Blueprint $table) {

            $table->dropForeign(['sale_id']);
            $table->dropForeign(['product_id']);

            $table->dropColumn('sale_id');
            $table->dropColumn('product_id');
            $table->dropColumn('amount');
            $table->dropColumn('tracking_code');
            $table->dropColumn('tracking_status_enum');

            $table->unsignedBigInteger('plans_sale_id')->after('product_plan_sale_id');
            $table->foreign('plans_sale_id')->references('id')->on('plans_sales');
            $table->timestamp('tracking_date')->after('delivery_id');
            $table->string('description')->after('tracking_date');
        });
    }
}
