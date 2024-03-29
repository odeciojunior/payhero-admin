<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
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
        VALUES (13,7,'simpay_production','WwJBUEl/VE9LRU4CGgJWU1RHTE8YR0ZDRBNEFRFDGEMUQhYYRBkVRRcUERETRkYURkRFFRUTGBVEFBdBFRIQREVDFxMTExdDQ0YXEhIUREYQFxMQQxUCXQ==',1,1,null,NOW(),NOW());");

        $jsonConfig2 = FoxUtils::xorEncrypt(
            json_encode([
                "api_token" => "",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (14,7,'simpay_sandbox','{$jsonConfig2}',0,1,null,NOW(),NOW());");

        $flags = [
            ["gateway_id" => 13, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 13, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 13],
            ["gateway_id" => 13, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 13, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 13, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 13, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],

            ["gateway_id" => 14, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 14, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 14, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 14],
            ["gateway_id" => 14, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 14, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 14, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
        ];

        $installmentsTax = [
            1 => 4.48,
            2 => 7.28,
            3 => 8.47,
            4 => 9.65,
            5 => 10.84,
            6 => 12.1,
            7 => 13.7,
            8 => 14.8,
            9 => 15.99,
            10 => 17.2,
            11 => 18.4,
            12 => 19.89,
        ];

        foreach ($flags as $flag) {
            $gatewayFlag = GatewayFlag::create($flag);

            for ($i = 1; $i <= 12; $i++) {
                GatewayFlagTax::create([
                    "gateway_flag_id" => $gatewayFlag->id,
                    "installments" => $i,
                    "type_enum" => 1,
                    "percent" => $installmentsTax[$i],
                ]);
            }
        }

        $flags = [
            ["gateway_id" => 13, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],
            ["gateway_id" => 14, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],
        ];
        foreach ($flags as $flag) {
            $gatewayFlag = GatewayFlag::create($flag);

            GatewayFlagTax::create([
                "gateway_flag_id" => $gatewayFlag->id,
                "installments" => 1,
                "type_enum" => 1,
                "percent" => 1.0,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM `gateway_flag_taxes` WHERE gateway_flag_id in (SELECT id FROM gateway_flags WHERE gateway_id in (13, 14));");

        DB::statement("DELETE FROM `gateway_flags` WHERE gateway_id in (13, 14);");

        DB::statement("DELETE FROM `gateways` WHERE id in (13, 14);");
    }
};
