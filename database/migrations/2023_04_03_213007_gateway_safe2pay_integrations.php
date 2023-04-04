<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        $jsonConfig = FoxUtils::xorEncrypt(json_encode([]));
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (1,1,'vega_production','{$jsonConfig}',1,1,null,NOW(),NOW());");

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (2,1,'vega_sandbox','{$jsonConfig}',0,1,null,NOW(),NOW());");

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (3,2,'safe2pay_production','WwJUT0tFTgIaAmITGBcVGBQVYxJmFxRmYWNhF2FiEWVjZWQUZRgZERFmAgwCU0VDUkVUf0tFWQIaAhNjFBMWZWVhYhAUFhQWFBFiFGYWFBcQYxUXGWIVFGZiExcQEWUSFBUWZBURFGZiFRhkExljEhFhYWMYZGIVFmUCXQ==',1,1,null,NOW(),NOW());");

        $jsonConfig2 = FoxUtils::xorEncrypt(
            json_encode([
                "token" => "F12F8CD5F3C147E0941993568332A5C3",
                "secret_key" => "D681B29A959E44FA9621413F70D2EA238A0321C81ADA44229D2B1C88A1206BB1",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (4,2,'safe2pay_sandbox','{$jsonConfig2}',0,1,null,NOW(),NOW());");
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
