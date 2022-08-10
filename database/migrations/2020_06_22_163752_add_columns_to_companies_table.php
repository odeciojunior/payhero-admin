<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table
                ->date("federal_registration_status_date")
                ->nullable()
                ->after("get_net_status");
            $table
                ->unsignedInteger("social_value")
                ->nullable()
                ->after("federal_registration_status_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn(["federal_registration_status_date", "social_value"]);
        });
    }
}
