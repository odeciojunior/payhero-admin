<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableGatewaysCompaniesCredencialsAddColumnHasWebhook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("gateways_companies_credentials", function (Blueprint $table) {
            $table
                ->tinyInteger("has_webhook")
                ->nullable()
                ->after("capture_transaction_enabled");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("gateways_companies_credentials", function (Blueprint $table) {
            $table->dropColumn("has_webhook");
        });
    }
}
