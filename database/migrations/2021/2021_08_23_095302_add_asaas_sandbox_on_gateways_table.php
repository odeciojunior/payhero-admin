<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class AddAsaasSandboxOnGatewaysTable extends Migration
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
                "name" => "asaas_sandbox",
            ],
            [
                "gateway_enum" => 8,
                "name" => "asaas_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "api_key" => "38c00b1d6be707d39a31dd59404feb02ac6fbf6ab5b111669c8ed3263c5e3ac2",
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
