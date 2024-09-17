<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        $gateways = [
            [
                "id" => 17,
                "name" => "payup_production",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "api_key" => "",
                    ]),
                ),
                "production_flag" => 1,
            ],
            [
                "id" => 18,
                "name" => "payup_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "api_key" => "",
                    ]),
                ),
                "production_flag" => 0,
            ],
        ];

        foreach ($gateways as $gateway) {
            DB::table("gateways")->insert([
                "id" => $gateway["id"],
                "gateway_enum" => 10,
                "name" => $gateway["name"],
                "json_config" => $gateway["json_config"] ?? '',
                "production_flag" => $gateway["production_flag"],
                "enabled_flag" => 1,
                "deleted_at" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $flags = [
            ["slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 13],
            ["slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
        ];

        foreach ($flags as $flag) {
            foreach ($gateways as $gateway) {
                $gatewayFlag = GatewayFlag::create([
                    "gateway_id" => $gateway["id"],
                    "slug" => $flag["slug"],
                    "name" => $flag["name"],
                    "card_flag_enum" => $flag["card_flag_enum"],
                ]);

                $installmentsTax = $this->getInstallmentsTax($flag["slug"]);
                foreach ($installmentsTax as $installments => $percent) {
                    GatewayFlagTax::create([
                        "gateway_flag_id" => $gatewayFlag->id,
                        "installments" => $installments,
                        "type_enum" => 1,
                        "percent" => $percent,
                    ]);
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
                    ->whereIn("gateway_id", [17, 18]);
            })
            ->delete();

        DB::table("gateway_flags")
            ->whereIn("gateway_id", [17, 18])
            ->delete();
        DB::table("gateways")
            ->whereIn("id", [17, 18])
            ->delete();
    }

    private function getInstallmentsTax($slug)
    {
        $taxes = [
            "visa" => [
                1 => 4.68,
                2 => 6.58,
                3 => 7.64,
                4 => 8.72,
                5 => 9.78,
                6 => 10.85,
                7 => 13.26,
                8 => 14.31,
                9 => 15.37,
                10 => 16.44,
                11 => 17.49,
                12 => 18.55,
            ],
            "mastercard" => [
                1 => 4.65,
                2 => 6.8,
                3 => 7.87,
                4 => 8.93,
                5 => 10.0,
                6 => 11.06,
                7 => 13.23,
                8 => 14.29,
                9 => 15.34,
                10 => 16.41,
                11 => 17.47,
                12 => 18.52,
            ],
            "elo" => [
                1 => 6.03,
                2 => 8.15,
                3 => 9.19,
                4 => 10.25,
                5 => 11.3,
                6 => 12.35,
                7 => 14.74,
                8 => 15.78,
                9 => 16.83,
                10 => 17.87,
                11 => 18.91,
                12 => 19.95,
            ],
            "hipercard" => [
                1 => 5.19,
                2 => 7.67,
                3 => 8.73,
                4 => 9.78,
                5 => 10.84,
                6 => 11.89,
                7 => 14.19,
                8 => 15.23,
                9 => 16.28,
                10 => 17.33,
                11 => 18.38,
                12 => 19.42,
            ],
            "amex" => [
                1 => 6.0,
                2 => 7.84,
                3 => 8.9,
                4 => 9.95,
                5 => 11.01,
                6 => 12.07,
                7 => 14.22,
                8 => 15.26,
                9 => 16.31,
                10 => 17.36,
                11 => 18.4,
                12 => 19.45,
            ],
        ];

        return $taxes[$slug] ?? [];
    }
};
