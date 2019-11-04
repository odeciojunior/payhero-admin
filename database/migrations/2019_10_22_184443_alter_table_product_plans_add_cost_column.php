<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductPlansAddCostColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('products_plans', function(Blueprint $table) {
            $table->unsignedInteger('cost')->nullable();
            $table->unsignedInteger('currency_type_enum')->default(1); //1- BRL, 2- USD
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('products_plans', function(Blueprint $table) {
            $table->dropColumn('cost');
            $table->dropColumn('currency_type_enum');
        });
    }
}
