<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->index("shopify_order");
            $table->index("woocommerce_order");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->dropIndex(["shopify_order"]);
            $table->dropIndex(["woocommerce_order"]);
        });
    }
}
