<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddApplyOnPlansToShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("shippings", function (Blueprint $table) {
            $table
                ->json("apply_on_plans")
                ->after("pre_selected")
                ->nullable();
            $table
                ->json("not_apply_on_plans")
                ->after("apply_on_plans")
                ->nullable();
        });
        DB::statement('update shippings set apply_on_plans = \'["all"]\', not_apply_on_plans = \'[]\'');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("shippings", function (Blueprint $table) {
            $table->removeColumn("apply_on_plans");
            $table->removeColumn("not_apply_on_plans");
        });
    }
}
