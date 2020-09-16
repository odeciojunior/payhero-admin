<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class SetBraspagProductionGatewaysTable extends Migration
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
                "name" => "braspag_production",
            ],
            [
                "gateway_enum" => 14,
                "name" => "braspag_production",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode(
                        [
                            "public_token" => "eb25ce51-f685-41c5-a76a-d8ed09f373c9",  //BRASPAG_CLIENT_ID_PRODUCTION
                            "private_token" => "yFFWG75RNkfte0WIgRPUlnHdSAKtJ3GYnaNdH8GVVAE=",  //BRASPAG_CLIENT_SECRET_PRODUCTION
                        ]
                    )),
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
