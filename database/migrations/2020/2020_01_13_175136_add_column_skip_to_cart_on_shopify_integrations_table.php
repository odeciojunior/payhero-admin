<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSkipToCartOnShopifyIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("shopify_integrations", function (Blueprint $table) {
            $table->boolean("skip_to_cart")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("shopify_integrations", function (Blueprint $table) {
            $table->dropColumn(["skip_to_cart"]);
        });
    }
}
