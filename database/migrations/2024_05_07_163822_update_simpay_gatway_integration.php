<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE `gateways` SET `json_config` = 'WwJBUEl/VE9LRU4CGgJWU1RNT1dFTkMYExQYQRFFGUUREBFGQxFFQ0VFQkEXFkUYGUZBEkYSGBEUGRJDERIXFRBDFEYYQxVFFBcWQhdCEUUYEBcSEBICXQ==' WHERE `id` = 13;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE `gateways` SET `json_config` = 'WwJBUEl/VE9LRU4CGgJWU1RHTE8YR0ZDRBNEFRFDGEMUQhYYRBkVRRcUERETRkYURkRFFRUTGBVEFBdBFRIQREVDFxMTExdDQ0YXEhIUREYQFxMQQxUCXQ==' WHERE `id` = 13;");
    }
};
