<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductAmountSelectorToProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->boolean("product_amount_selector")->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->dropColumn("product_amount_selector");
        });
    }
}
