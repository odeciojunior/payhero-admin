<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDbForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliates', function(Blueprint $table) {
            $table->renameColumn('user', 'user_id');
            $table->renameColumn('project', 'project_id');
            $table->renameColumn('company', 'company_id');
        });

        Schema::table('affiliate_links', function(Blueprint $table) {
            $table->renameColumn('affiliate', 'affiliate_id');
            $table->renameColumn('campaign', 'campaign_id');
            $table->renameColumn('plan', 'plan_id');
        });

        Schema::table('affiliate_requests', function(Blueprint $table) {
            $table->renameColumn('user', 'user_id');
            $table->renameColumn('project', 'project_id');
        });

        Schema::table('campaigns', function(Blueprint $table) {
            $table->renameColumn('affiliate', 'affiliate_id');
        });

        Schema::table('checkouts', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });

        Schema::table('checkout_plans', function(Blueprint $table) {
            $table->renameColumn('checkout', 'checkout_id');
            $table->renameColumn('plan', 'plan_id');
        });

        Schema::table('clients_cookie', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
            $table->renameColumn('affiliate', 'affiliate_id');
        });

        Schema::table('deliveries', function(Blueprint $table) {
            $table->renameColumn('carrier', 'carrier_id');
        });

        Schema::table('discount_coupons', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });

        Schema::table('extra_materials', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });

        Schema::table('logs', function(Blueprint $table) {
            $table->unsignedBigInteger('checkout_id')->nullable();
            $table->foreign('checkout_id')->references('id')->on('checkouts');
        });

        Schema::table('pixels', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });

        Schema::table('plans', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });

        Schema::table('plans_sales', function(Blueprint $table) {
            $table->renameColumn('plan', 'plan_id');
            $table->renameColumn('sale', 'sale_id');
        });

        Schema::table('plan_gifts', function(Blueprint $table) {
            $table->renameColumn('gift', 'gift_id');
            $table->renameColumn('plan', 'plan_id');
        });

        Schema::table('products', function(Blueprint $table) {
            $table->renameColumn('category', 'category_id');
            $table->renameColumn('user', 'user_id');
        });

        Schema::table('products_plans', function(Blueprint $table) {
            $table->renameColumn('product', 'product_id');
            $table->renameColumn('plan', 'plan_id');
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->renameColumn('carrier', 'carrier_id');
        });

        Schema::table('sales', function(Blueprint $table) {
            $table->renameColumn('owner', 'owner_id');
            $table->renameColumn('affiliate', 'affiliate_id');
            $table->renameColumn('client', 'client_id');
            $table->renameColumn('delivery', 'delivery_id');
            $table->renameColumn('shipping', 'shipping_id');
            $table->renameColumn('project', 'project_id');
            $table->renameColumn('checkout', 'checkout_id');
        });

        Schema::table('shopify_integrations', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
            $table->renameColumn('user', 'user_id');
        });

        Schema::table('transactions', function(Blueprint $table) {
            $table->renameColumn('sale', 'sale_id');
            $table->renameColumn('company', 'company_id');
        });

        Schema::table('transfers', function(Blueprint $table) {
            $table->renameColumn('transaction', 'transaction_id');
            $table->renameColumn('user', 'user_id');
        });

        Schema::table('users_projects', function(Blueprint $table) {
            $table->renameColumn('user', 'user_id');
            $table->renameColumn('project', 'project_id');
            $table->renameColumn('company', 'company_id');
        });

        Schema::table('zenvia_sms', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
            $table->renameColumn('plan', 'plan_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliates', function(Blueprint $table) {
            $table->renameColumn('user_id', 'user');
            $table->renameColumn('project_id', 'project');
            $table->renameColumn('company_id', 'company');
        });

        Schema::table('affiliate_links', function(Blueprint $table) {
            $table->renameColumn('affiliate_id', 'affiliate');
            $table->renameColumn('campaign_id', 'campaign');
            $table->renameColumn('plan_id', 'plan');
        });

        Schema::table('affiliate_requests', function(Blueprint $table) {
            $table->renameColumn('user_id', 'user');
            $table->renameColumn('project_id', 'project');
        });

        Schema::table('campaigns', function(Blueprint $table) {
            $table->renameColumn('affiliate_id', 'affiliate');
        });

        Schema::table('checkouts', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });

        Schema::table('checkout_plans', function(Blueprint $table) {
            $table->renameColumn('checkout_id', 'checkout');
            $table->renameColumn('plan_id', 'plan');
        });

        Schema::table('clients_cookie', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
            $table->renameColumn('affiliate_id', 'affiliate');
        });

        Schema::table('deliveries', function(Blueprint $table) {
            $table->renameColumn('carrier_id', 'carrier');
        });

        Schema::table('discount_coupons', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });

        Schema::table('extra_materials', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });

        Schema::table('logs', function(Blueprint $table) {
            $table->dropColumn('checkout_id');
        });

        Schema::table('pixels', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });

        Schema::table('plans', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });

        Schema::table('plans_sales', function(Blueprint $table) {
            $table->renameColumn('plan_id', 'plan');
            $table->renameColumn('sale_id', 'sale');
        });

        Schema::table('plan_gifts', function(Blueprint $table) {
            $table->renameColumn('gift_id', 'gift');
            $table->renameColumn('plan_id', 'plan');
        });

        Schema::table('products', function(Blueprint $table) {
            $table->renameColumn('category_id', 'category');
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('products_plans', function(Blueprint $table) {
            $table->renameColumn('product_id', 'product');
            $table->renameColumn('plan_id', 'plan');
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->renameColumn('carrier_id', 'carrier');
        });

        Schema::table('sales', function(Blueprint $table) {
            $table->renameColumn('owner_id', 'owner');
            $table->renameColumn('affiliate_id', 'affiliate');
            $table->renameColumn('client_id', 'client');
            $table->renameColumn('delivery_id', 'delivery');
            $table->renameColumn('shipping_id', 'shipping');
            $table->renameColumn('project_id', 'project');
            $table->renameColumn('checkout_id', 'checkout');
        });

        Schema::table('shopify_integrations', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('transactions', function(Blueprint $table) {
            $table->renameColumn('sale_id', 'sale');
            $table->renameColumn('company_id', 'company');
        });

        Schema::table('transfers', function(Blueprint $table) {
            $table->renameColumn('transaction_id', 'transaction');
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('users_projects', function(Blueprint $table) {
            $table->renameColumn('user_id', 'user');
            $table->renameColumn('project_id', 'project');
            $table->renameColumn('company_id', 'company');
        });

        Schema::table('zenvia_sms', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
            $table->renameColumn('plan_id', 'plan');
        });
    }
}
