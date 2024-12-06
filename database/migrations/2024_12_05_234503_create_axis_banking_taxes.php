<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gateways = [
            [
                "id" => 25,
            ],
            [
                "id" => 26,
            ],
        ];

        $flags = [
            ["gateway_id" => 0, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 0, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 9],
            ["gateway_id" => 0, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 0, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 0, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 0, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
            ["gateway_id" => 0, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],
        ];

        $installmentsTaxes = [
            "visa" => [
                1 => 3.56,
                2 => 4.13,
                3 => 4.13,
                4 => 4.13,
                5 => 4.13,
                6 => 4.13,
                7 => 4.46,
                8 => 4.46,
                9 => 4.46,
                10 => 4.46,
                11 => 4.46,
                12 => 4.46,
            ],
            "master" => [
                1 => 3.56,
                2 => 4.13,
                3 => 4.13,
                4 => 4.13,
                5 => 4.13,
                6 => 4.13,
                7 => 4.46,
                8 => 4.46,
                9 => 4.46,
                10 => 4.46,
                11 => 4.46,
                12 => 4.46,
            ],
            "elo" => [
                1 => 4.56,
                2 => 5.13,
                3 => 5.13,
                4 => 5.13,
                5 => 5.13,
                6 => 5.13,
                7 => 5.46,
                8 => 5.46,
                9 => 5.46,
                10 => 5.46,
                11 => 5.46,
                12 => 5.46,
            ],
            "outros" => [
                1 => 4.06,
                2 => 4.63,
                3 => 4.63,
                4 => 4.63,
                5 => 4.63,
                6 => 4.63,
                7 => 4.96,
                8 => 4.96,
                9 => 4.96,
                10 => 4.96,
                11 => 4.96,
                12 => 4.96,
            ],
        ];

        foreach ($gateways as $gateway) {
            foreach ($flags as $flag) {
                $flag["gateway_id"] = $gateway["id"];
                $gatewayFlag = GatewayFlag::create($flag);

                switch ($flag["slug"]) {
                    case "pix":
                        GatewayFlagTax::create([
                            "gateway_flag_id" => $gatewayFlag->id,
                            "installments" => 0,
                            "type_enum" => 3,
                            "percent" => 0,
                        ]);
                        break;
                    default:
                        $brand = in_array($gatewayFlag->slug, ["visa", "master", "elo"])
                            ? $gatewayFlag->slug
                            : "outros";
                        foreach ($installmentsTaxes[$brand] as $key2 => $installmentTax) {
                            GatewayFlagTax::create([
                                "gateway_flag_id" => $gatewayFlag->id,
                                "installments" => $key2,
                                "type_enum" => 1,
                                "percent" => $installmentTax,
                            ]);
                        }
                        break;
                }
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
        DB::table("gateway_flag_taxes")
            ->whereIn("gateway_flag_id", function ($query) {
                $query
                    ->select("id")
                    ->from("gateway_flags")
                    ->whereIn("gateway_id", [25, 26]);
            })
            ->delete();
    }
};
