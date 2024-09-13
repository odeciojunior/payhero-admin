<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            "ALTER TABLE transfer_gateway_postbacks ADD COLUMN `type` ENUM('payment','validation') NOT NULL DEFAULT 'payment' AFTER machine_result ;"
        );
        DB::statement(
            "ALTER TABLE transfer_gateway_requests ADD COLUMN `type` ENUM('payment','validation') NOT NULL DEFAULT 'payment' AFTER response ;"
        );
        DB::statement(
            "ALTER TABLE `company_bank_accounts`
            CHANGE COLUMN `bank_ispb` `bank_ispb` VARCHAR(10) NULL COLLATE 'utf8mb4_unicode_ci' AFTER `bank`;"
        );
    }
};
