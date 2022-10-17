<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Services\FoxUtils;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `companies`
        CHANGE COLUMN `safe2pay_balance` `vega_balance` INT(10) NOT NULL DEFAULT '0';");

        $jsonConfig = FoxUtils::xorEncrypt(json_encode([]));

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (25,19,'vega_production','{$jsonConfig}',1,1,null,NOW(),NOW());");

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (26,19,'vega_sandbox','{$jsonConfig}',0,1,null,NOW(),NOW());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
