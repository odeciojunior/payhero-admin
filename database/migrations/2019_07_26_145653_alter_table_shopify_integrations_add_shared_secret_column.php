<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableShopifyIntegrationsAddSharedSecretColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopify_integrations', function(Blueprint $table) {
            $table->string('shared_secret')->after('token');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopify_integrations', function(Blueprint $table) {
            $table->dropColumn('shared_secret');
        });
    }
}
