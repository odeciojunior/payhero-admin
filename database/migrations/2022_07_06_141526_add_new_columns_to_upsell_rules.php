<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToUpsellRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("project_upsell_rules", function (Blueprint $table) {
            $table
                ->json("apply_on_shipping")
                ->nullable()
                ->after("discount");
            $table
                ->boolean("use_variants")
                ->default(true)
                ->after("apply_on_shipping");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("project_upsell_rules", function (Blueprint $table) {
            $table->dropColumn(["apply_on_shipping", "use_variants"]);
        });
    }
}
