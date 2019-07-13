<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopifyIntegrationsAddTemplateColumns extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('shopify_integrations', function(Blueprint $table) {
            $table->integer('theme_type')->nullable();
            $table->string('theme_name')->nullable();
            $table->text('theme_file')->nullable();
            $table->text('theme_html')->nullable();
            $table->text('layout_theme_html')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('shopify_integrations', function(Blueprint $table) {
            $table->dropColumn('theme_type');
            $table->dropColumn('theme_name');
            $table->dropColumn('theme_file');
            $table->dropColumn('theme_html');
            $table->dropColumn('layout_theme_html');
        });
    }
}
