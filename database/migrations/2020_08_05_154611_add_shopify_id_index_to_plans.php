<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddShopifyIdIndexToPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE plans ROW_FORMAT=DYNAMIC;");
        Schema::table("plans", function (Blueprint $table) {
            $table->index("shopify_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("plans", function (Blueprint $table) {
            $table->dropIndex(["shopify_id"]);
        });
    }
}
