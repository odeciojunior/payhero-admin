<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservationColumnToAbandonedSignupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("abandoned_signups", function (Blueprint $table) {
            $table
                ->string("observation")
                ->nullable()
                ->after("monthly_income");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("abandoned_signups", function (Blueprint $table) {
            $table->dropColumn("observation");
        });
    }
}
