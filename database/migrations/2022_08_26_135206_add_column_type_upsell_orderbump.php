<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTypeUpsellOrderbump extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_bump_rules', function (Blueprint $table) {
            $table->tinyInteger("type")->default(0)->nullable()->after("discount");
            $table->float('discount')->change();
        });

        Schema::table('project_upsell_rules', function (Blueprint $table) {
            $table->tinyInteger("type")->default(0)->nullable()->after("discount");
            $table->float('discount')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('order_bump_rules', function (Blueprint $table) {
            $table->dropColumn("type");
            $table->integer('discount')->change();
        });
        Schema::table('project_upsell_rules', function (Blueprint $table) {
            $table->dropColumn("type");
            $table->integer('discount')->change();
        });
    }
}
