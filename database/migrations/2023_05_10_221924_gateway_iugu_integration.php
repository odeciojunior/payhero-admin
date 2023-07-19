<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Services\FoxUtils;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (7,4,'iugu_production','WwJUT0tFTgIaAhIYFWZjFhAUFmMQZBgZFWUSFBQSZBZiZmZlFxdlYhcQFGUYFRQUFmYZZWQREhcXEBcSFGFhGBFhGRViYxUWFRECDAJBQ0NPVU5Uf0lEAhoCF2RmFhRiFhEQGBkVFGJkZRkXFhkSYWRjF2USY2VkGRgCXQ==',1,1,null,NOW(),NOW());");

        $jsonConfig2 = FoxUtils::xorEncrypt(
            json_encode([
                "token" => "8E66FE55F6D63F4D965BB65B7A4D0B8E0D28DBFBA3AFBE3E511D1ED8F5D4DC1E",
                "account_id" => "7DF64B6108954BDE97692ADC7E2CED98",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (8,4,'iugu_sandbox','{$jsonConfig2}',0,1,null,NOW(),NOW());");
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
