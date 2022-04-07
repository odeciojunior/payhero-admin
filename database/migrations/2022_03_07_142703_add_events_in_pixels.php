<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventsInPixels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pixels', function (Blueprint $table) {
            $table->boolean('send_value_checkout')->default(false)->after('checkout');
            $table->boolean('basic_data')->default(true)->after('send_value_checkout');
            $table->boolean('delivery')->default(true)->after('basic_data');
            $table->boolean('coupon')->default(true)->after('delivery');
            $table->boolean('payment_info')->default(true)->after('coupon');
            $table->boolean('upsell')->default(true)->after('payment_info');
            $table->boolean('purchase_upsell')->default(true)->after('upsell');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pixels', function (Blueprint $table) {
            $table->dropColumn('send_value_checkout');
            $table->dropColumn('basic_data');
            $table->dropColumn('delivery');
            $table->dropColumn('coupon');
            $table->dropColumn('payment_info');
            $table->dropColumn('upsell');
            $table->dropColumn('purchase_upsell');
        });
    }
}
