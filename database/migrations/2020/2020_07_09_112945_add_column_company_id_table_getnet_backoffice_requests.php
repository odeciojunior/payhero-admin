<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCompanyIdTableGetnetBackofficeRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("getnet_backoffice_requests", function (Blueprint $table) {
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->after("id");
        });
        Schema::table("getnet_backoffice_requests", function (Blueprint $table) {
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("getnet_backoffice_requests", function ($table) {
            $table->dropForeign(["company_id"]);

            $table->dropColumn("company_id");
        });
    }
}
