<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCompanyIdOnBraspagBackofficeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("braspag_backoffice_requests", function (Blueprint $table) {
            $table->integer("company_id")->unsigned();
        });

        Schema::table("braspag_backoffice_requests", function (Blueprint $table) {
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
        Schema::table("braspag_backoffice_requests", function (Blueprint $table) {
            $table->dropColumn("company_id");
        });
    }
}
