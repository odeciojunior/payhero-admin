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
        DB::statement("ALTER TABLE `company_bank_accounts`
        CHANGE COLUMN `bank_ispb` `bank_ispb` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `bank`;");

        Schema::table("transfer_gateway_requests", function (Blueprint $table) {
            $table
                ->enum("type", ["payment", "validation"])
                ->default("payment")
                ->after("response");
        });

        Schema::table("transfer_gateway_postbacks", function (Blueprint $table) {
            $table
                ->enum("type", ["payment", "validation"])
                ->default("payment")
                ->after("machine_result");
        });
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
};
