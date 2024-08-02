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
                "id" => 19,
                "name" => "malga_production",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "client_id" => "",
                        "api_key" => "",
                        "merchant_id" => "",
                    ]),
                ),
                "production_flag" => 1,
            ],
            [
                "id" => 20,
                "name" => "malga_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "client_id" => "",
                        "api_key" => "",
                        "merchant_id" => "",
                    ]),
                ),
                "production_flag" => 1,
            ],
        ];

        foreach ($gateways as $gateway) {
            DB::table("gateways")->insert([
                "id" => $gateway["id"],
                "gateway_enum" => 9,
                "name" => $gateway["name"],
                "json_config" => $gateway["json_config"],
                "production_flag" => $gateway["production_flag"],
                "enabled_flag" => 1,
                "deleted_at" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $flags = [
            ["slug" => "card", "name" => "Card", "card_flag_enum" => 1],
            ["slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 13],
            ["slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
        ];

        foreach ($flags as $flag) {
            foreach ($gateways as $gateway) {
                $gatewayFlag = GatewayFlag::create([
                    "gateway_id" => $flag["slug"] === "card" ? null : $gateway["id"],
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
                    ->whereIn("gateway_id", [19, 20]);
            })
            ->delete();

        DB::table("gateway_flags")
            ->whereIn("gateway_id", [19, 20])
            ->delete();
        DB::table("gateways")
            ->whereIn("id", [19, 20])
            ->delete();
    }

    private function getInstallmentsTax($slug)
    {
        $taxes = [
            "card" => [
                1 => 3.03,
                2 => 3.68,
                3 => 3.68,
                4 => 3.68,
                5 => 3.68,
                6 => 3.68,
                7 => 3.93,
                8 => 3.93,
                9 => 3.93,
                10 => 3.93,
                11 => 3.93,
                12 => 3.93,
            ],
            "mastercard" => [
                1 => 3.03,
                2 => 3.68,
                3 => 3.68,
                4 => 3.68,
                5 => 3.68,
                6 => 3.68,
                7 => 3.93,
                8 => 3.93,
                9 => 3.93,
                10 => 3.93,
                11 => 3.93,
                12 => 3.93,
            ],
            "visa" => [
                1 => 3.12,
                2 => 3.34,
                3 => 3.34,
                4 => 3.34,
                5 => 3.34,
                6 => 3.34,
                7 => 3.68,
                8 => 3.68,
                9 => 3.68,
                10 => 3.68,
                11 => 3.68,
                12 => 3.68,
            ],
            "elo" => [
                1 => 3.56,
                2 => 3.81,
                3 => 3.81,
                4 => 3.81,
                5 => 3.81,
                6 => 3.81,
                7 => 4.16,
                8 => 4.16,
                9 => 4.16,
                10 => 4.16,
                11 => 4.16,
                12 => 4.16,
            ],
            "amex" => [
                1 => 3.56,
                2 => 3.82,
                3 => 3.82,
                4 => 3.82,
                5 => 3.82,
                6 => 3.82,
                7 => 3.97,
                8 => 3.97,
                9 => 3.97,
                10 => 3.97,
                11 => 3.97,
                12 => 3.97,
            ],
            "hipercard" => [
                1 => 3.03,
                2 => 3.68,
                3 => 3.68,
                4 => 3.68,
                5 => 3.68,
                6 => 3.68,
                7 => 3.93,
                8 => 3.93,
                9 => 3.93,
                10 => 3.93,
                11 => 3.93,
                12 => 3.93,
            ],
        ];

        return $taxes[$slug] ?? [];
    }
};
