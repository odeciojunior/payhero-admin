<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class SetBraspagCredentialsGatewaysTable extends Migration
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
                "name" => "braspag_sandbox",
            ],
            [
                "gateway_enum" => 14,
                "name" => "braspag_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "public_token" => "eb25ce51-f685-41c5-a76a-d8ed09f373c9",
                        "private_token" => "yFFWG75RNkfte0WIgRPUlnHdSAKtJ3GYnaNdH8GVVAE=",
                    ])
                ),
                "production_flag" => 0,
                "enabled_flag" => 1,
            ]
        );

        Gateway::updateOrCreate(
            [
                "name" => "braspag_production",
            ],
            [
                "gateway_enum" => 15,
                "name" => "braspag_production",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "public_token" => "CED58F54-719E-4B03-A212-5F164CFD0A77",
                        "private_token" => "qNR+Ze91jfhn75KqGCK9hYSlVEOexPJC4Z575tKQCUU=",
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
