<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductPlanSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_plans_sales', function (Blueprint $table) 
        {
            $table->unsignedBigInteger('plan_id')->nullable()->change();
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->dropColumn('tracking_code');
            $table->dropColumn('tracking_type_enum');
            $table->dropColumn('tracking_status_enum');

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
