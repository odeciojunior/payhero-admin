<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FillTranckingsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            INSERT INTO products_plans_sales
                (product_id
                , plan_id
                , sale_id
                , name
                , description
                , guarantee
                , format
                , cost
                , photo
                , height
                , weight
                , shopify
                , shopify_id
                , shopify_variant_id
                , created_at
                , updated_at)
                SELECT pp.product_id
                , p.id plan_id
                , ps.sale_id
                , pr.name
                , pr.description
                , pr.guarantee
                , pr.format
                , pr.cost
                , pr.photo
                , pr.height
                , pr.weight
                , pr.shopify
                , pr.shopify_id
                , pr.shopify_variant_id
                , ps.created_at
                , ps.updated_at
                FROM plans p
                INNER JOIN plans_sales ps
                ON p.id = ps.plan_id
                INNER JOIN products_plans pp
                ON p.id = pp.plan_id
                INNER JOIN products pr
                ON pp.product_id = pr.id
                ORDER BY sale_id, plan_id, product_id
                ;
        ");
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        DB::unprepared("DELETE FROM product_plan_sales;");
    }
}
