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
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (11,6,'abmex_production','WwJTRUNSRVR/S0VZAhoCU0t/TElWRX9kWVVhFkxGb1F3SnITbRVPeFAQTHdGTlBEFVN5FhNUaURLb1Rwd1hIFWkCDAJQVUJMSUN/S0VZAhoCUEt/TElWRX9TU0FVaBVMbHhLdGZWWHNmWlpwdVN2eWJ0RFhNSlYCXQ==',1,1,null,NOW(),NOW());");

        $jsonConfig2 = FoxUtils::xorEncrypt(
            json_encode([
                "secret_key" => "",
                "public_key" => "",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (12,6,'abmex_sandbox','{$jsonConfig2}',0,1,null,NOW(),NOW());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("abmex_gateway_integration");
    }
};
