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
        VALUES (15,8,'efipay_production','WwJDTElFTlR/SUQCGgJjTElFTlR/aUR/RBYTQxYWRRZFFxQUGBMVGUZEFRIVQkEQFBMRREREEkYTQREWFBRFEgIMAkNMSUVOVH9TRUNSRVQCGgJjTElFTlR/c0VDUkVUfxdBQRVEGRRBFRgVEhFEExYYFhJGQkFFRhEYRRcQGRIRQ0ZCExITRRcCXQ==',1,1,null,NOW(),NOW());");

        $jsonConfig2 = FoxUtils::xorEncrypt(
            json_encode([
                "client_id" => "Client_Id_de24a90ad63f16e9d8b9f699c145a7aa55481960",
                "client_secret" => "Client_Secret_add519b499471647301ae9c1c88429b29d0dd320",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (16,8,'efipay_sandbox','{$jsonConfig2}',0,1,null,NOW(),NOW());");

        $flags = [
            ["gateway_id" => 15, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 15, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 15],
            ["gateway_id" => 15, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 15, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 15, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 15, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],

            ["gateway_id" => 16, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 16, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 16, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 14],
            ["gateway_id" => 16, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 16, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 16, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
        ];

        $installmentsTax = [
            1 => 3.49,
            2 => 4.48,
            3 => 5.47,
            4 => 6.46,
            5 => 7.45,
            6 => 8.44,
            7 => 9.43,
            8 => 10.42,
            9 => 11.41,
            10 => 12.4,
            11 => 13.39,
            12 => 14.38,
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
