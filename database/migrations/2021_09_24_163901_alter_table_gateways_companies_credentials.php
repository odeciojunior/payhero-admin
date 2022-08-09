<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableGatewaysCompaniesCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `gateways_companies_credentials`
	    CHANGE COLUMN `gateway_api_key` `gateway_api_key` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `gateway_subseller_id`;");

        //        DB::statement("ALTER TABLE `gateways_companies_credentials`
        //        ADD UNIQUE INDEX `credentials` (`company_id`, `gateway_id`);");

        DB::statement("ALTER TABLE `gateways_companies_credentials`
        CHANGE COLUMN `gateway_subseller_id` `gateway_subseller_id` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `gateway_status`;");

        DB::statement("ALTER TABLE `gateways_companies_credentials`
        ADD COLUMN `capture_transaction_enabled` TINYINT NULL DEFAULT NULL AFTER `gateway_api_key`;");
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
