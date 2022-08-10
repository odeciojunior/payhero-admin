<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class SetGetnetSandboxGatewaysTable extends Migration
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
                "name" => "getnet_sandbox",
            ],
            [
                "gateway_enum" => 12,
                "name" => "getnet_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "public_token" => "24c5e206-7aec-4365-8322-78aa067c6f81",
                        "private_token" => "201962f8-4b53-469f-98be-12dc370d1ead",
                    ])
                ),
                "production_flag" => 0,
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
