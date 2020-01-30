<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableShopifyIntegrationsChangeColumnTokenToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopify_integrations', function (Blueprint $table) {
            $table->string('token')->nullable()->change();        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopify_integrations', function (Blueprint $table) {
            $table->string('token')->nullable(false)->change();        
        });
    }
}
