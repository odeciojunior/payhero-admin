<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsTableAddColumnSku extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('products', 'sku')) {
            Schema::table('products', function ($table) {
                $table->string('sku')->change();
            });
        } else {
            Schema::table('products', function (Blueprint $table) {
                $table->string('sku')->nullable()->after('shopify_variant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function(Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
}
