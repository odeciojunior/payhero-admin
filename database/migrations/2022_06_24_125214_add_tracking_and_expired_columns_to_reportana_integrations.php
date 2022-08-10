<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingAndExpiredColumnsToReportanaIntegrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("reportana_integrations", function (Blueprint $table) {
            $table
                ->boolean("billet_expired")
                ->default(true)
                ->after("billet_paid");
            $table
                ->boolean("pix_expired")
                ->default(true)
                ->after("pix_paid");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("reportana_integrations", function (Blueprint $table) {
            $table->dropColumn(["billet_expired", "pix_expired"]);
        });
    }
}
