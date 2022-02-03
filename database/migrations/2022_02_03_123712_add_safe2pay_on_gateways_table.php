<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class AddSafe2payOnGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Gateway::updateOrCreate(
            [
                "name" => "safe2pay_sandbox",
            ],
            [
                "gateway_enum"    => 17,
                "name"            => "safe2pay_sandbox",
                "json_config"     => FoxUtils::xorEncrypt(
                    json_encode(
                        [
                            "api_key"  => "38c00b1d6be707d39a31dd59404feb02ac6fbf6ab5b111669c8ed3263c5e3ac2",
                        ]
                    )),
                "production_flag" => 0,
                "enabled_flag"    => 1,
            ]
        );
    
        Gateway::updateOrCreate(
            [
                "name" => "safe2pay_production",
            ],
            [
                "gateway_enum"    => 17,
                "name"            => "safe2pay_production",
                "json_config"     => FoxUtils::xorEncrypt(
                    json_encode(
                        [
                            "api_key"  => "38c00b1d6be707d39a31dd59404feb02ac6fbf6ab5b111669c8ed3263c5e3ac2",
                        ]
                    )),
                "production_flag" => 0,
                "enabled_flag"    => 1,
            ]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $safe2paySandbox = Gateway::where('name', 'safe2pay_sandbox')->first();
        $safe2paySandbox->forceDelete();

        $safe2payProduction = Gateway::where('name', 'safe2pay_production')->first();
        $safe2payProduction->forceDelete();
    }
}
