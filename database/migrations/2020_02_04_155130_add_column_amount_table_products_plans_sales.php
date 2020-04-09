<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAmountTableProductsPlansSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_plans_sales', function(Blueprint $table) {
            $table->integer('amount')->nullable()->after('sale_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_plans_sales', function(Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
}
