<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePixTransfersAddStatusReturned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `pix_transfers`
        CHANGE COLUMN `status` `status` ENUM('PROCESSING','REALIZED','UNREALIZED','RETURNED') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `transaction_ids`;");
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
