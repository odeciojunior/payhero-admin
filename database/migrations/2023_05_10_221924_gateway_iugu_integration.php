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
        VALUES (7,4,'iugu_production','WwJUT0tFTgIaAhFiEmYWF2VkEGYSEBMTGGETYWFkFGIWFREZYWQWEhQTZGUSF2MXY2NiEhQYZRYQYhRhZhUYE2NhEWMYYxYVEhUCDAJBQ0NPVU5Uf0lEAhoCFRYXERISGWFmFWUYFGQTERkUYWUSExZkGWUZERJlFBYCXQ==',1,1,null,NOW(),NOW());");

        $jsonConfig2 = FoxUtils::xorEncrypt(
            json_encode([
                "token" => "EAC7F953BF13867FDB4C32FFCC2955A48F1E6507C37F940CA7B561292B42B391",
                "account_id" => "5671229AF5E84D3194AE236D9E912E46",
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
