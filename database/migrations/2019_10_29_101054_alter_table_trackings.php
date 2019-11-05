<?php

use Illuminate\Support\Facades\DB;
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

        Schema::table('trackings', function (Blueprint $table) {
            $table->integer('amount')->nullable()->change();
        });

        DB::statement('insert into trackings (product_plan_sale_id, sale_id, product_id, amount, delivery_id, tracking_code, tracking_status_enum, created_at, updated_at)
                              select pps.id, pps.sale_id, pps.product_id, ((select amount from products_plans where plan_id = pps.plan_id and product_id = pps.product_id) * ps.amount )as amount, s.delivery_id, pps.tracking_code, if(pps.tracking_status_enum is null,0,pps.tracking_status_enum) as tracking_status_enum, now() as created_at, now() as updated_at
                              from products_plans_sales pps
                              join sales s on s.id = pps.sale_id
                              join plans_sales ps on ps.sale_id = pps.sale_id and ps.plan_id = pps.plan_id
                              where pps.tracking_code is not null');
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
