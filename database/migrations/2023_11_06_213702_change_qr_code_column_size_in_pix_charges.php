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
        DB::statement("ALTER TABLE `pix_charges`
        CHANGE COLUMN `qrcode` `qrcode` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `location`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `pix_charges`
        CHANGE COLUMN `qrcode` `qrcode` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `location`;");
    }
};
