<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->integer('checkout_step_type')->default(1);
            $table->integer('checkout_expanded_resume')->default(1);
            $table->integer('checkout_custom_border_radius')->default(1);
            $table->integer('checkout_custom_footer_enabled')->default(1);
            $table->text('checkout_custom_footer_message')->nullable();
            $table->integer('delivery_time_shipping_enabled')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->$table->dropColumn('checkout_step_type');
            $table->$table->dropColumn('checkout_expanded_resume');
            $table->$table->dropColumn('checkout_custom_border_radius');
            $table->$table->dropColumn('checkout_custom_footer_enabled');
            $table->$table->dropColumn('checkout_custom_footer_message');
            $table->$table->dropColumn('delivery_time_shipping_enabled');
        });
    }
};
