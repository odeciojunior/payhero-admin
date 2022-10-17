<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class SetGetProductionGatewaysTable extends Migration
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
                "name" => "getnet_production",
            ],
            [
                "gateway_enum" => 13,
                "name" => "getnet_production",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "public_token" => "c33acdd9-a0ad-4076-8fec-f4c2cbb193f1",
                        "private_token" => "513123f8-589b-49f2-ad1b-29b2e9133deb",
                    ])
                ),
                "production_flag" => 1,
                "enabled_flag" => 1,
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
        //
    }
}
