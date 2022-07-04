<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsInWooCommerceIntegrationsSync extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('woo_commerce_integrations_sync', function (Blueprint $table) {
            
            // //drop
            $table->dropColumn('product_data');
            $table->dropColumn('total_products');
            $table->dropColumn('total_products_done');
            $table->dropColumn('total_products_sku_compared');
            

            // //add
            $table->integer('tries')->default(0)->after('status');
            $table->json('result')->nullable()->after('status');
            $table->string('method')->nullable()->after('status');
            $table->json('data')->nullable()->after('status');
            $table->bigInteger('integration_id')->nullable()->after('status');
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('woo_commerce_integrations_sync', function (Blueprint $table) {
            $table->dropColumn('data');
            $table->dropColumn('method');
            $table->dropColumn('result');
            $table->dropColumn('integration_id');
            $table->dropColumn('tries');

            $table->longText('product_data');
            $table->integer('total_products')->default(0);
            $table->integer('total_products_done')->default(0);
            $table->integer('total_products_sku_compared')->default(0);

        });
    }
}
