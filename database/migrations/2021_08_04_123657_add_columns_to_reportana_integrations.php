<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToReportanaIntegrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reportana_integrations', function (Blueprint $table) {
            $table->boolean('pix_generated')
                ->default(true)
                ->after('credit_card_paid');
            $table->boolean('pix_paid')
                ->default(true)
                ->after('pix_generated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reportana_integrations', function (Blueprint $table) {
            $table->dropColumn(['pix_generated', 'pix_paid']);
        });
    }
}
