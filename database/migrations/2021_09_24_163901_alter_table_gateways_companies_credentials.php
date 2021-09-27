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

        DB::statement("ALTER TABLE `gateways_companies_credentials`
        ADD UNIQUE INDEX `credentials` (`company_id`, `gateway_id`);");
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
