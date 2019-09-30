<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTrackingCodeProductPlansSales extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('products_plans_sales', function(Blueprint $table) {
            $table->string('tracking_code')->after('shopify_variant_id')->nullable();
            $table->tinyInteger('tracking_type_enum')->after('shopify_variant_id')->nullable();
            $table->tinyInteger('tracking_status_enum')->after('shopify_variant_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('products_plans_sales', function(Blueprint $table) {
            $table->dropColumn('tracking_code');
            $table->dropColumn('tracking_type_enum');
            $table->dropColumn('tracking_status_enum');
        });
    }
}
