<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDiscountProjectUpsellRulesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('project_upsell_rules', function(Blueprint $table) {
            $table->integer('discount')->nullable()->after('offer_on_plans');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('project_upsell_rules', function(Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
}
