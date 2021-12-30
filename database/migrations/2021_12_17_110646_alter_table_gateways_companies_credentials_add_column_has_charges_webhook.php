<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableGatewaysCompaniesCredentialsAddColumnHasChargesWebhook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `gateways_companies_credentials`
        CHANGE COLUMN `has_webhook` `has_transfers_webhook` TINYINT(4) NULL DEFAULT NULL AFTER `capture_transaction_enabled`,
        ADD COLUMN `has_charges_webhook` TINYINT(4) NULL DEFAULT NULL AFTER `has_transfers_webhook`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
