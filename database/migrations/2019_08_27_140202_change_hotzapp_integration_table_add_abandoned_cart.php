<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeHotzappIntegrationTableAddAbandonedCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotzapp_integrations', function(Blueprint $table) {
            $table->boolean('abandoned_cart')->after('credit_card_paid')->default(true);
        });
    }

    /**
     * Reverse the migrations. 
     */
    public function down()
    {
        Schema::table('hotzapp_integrations', function(Blueprint $table) {
            $table->dropColumn('abandoned_cart');
        });
    }
}
