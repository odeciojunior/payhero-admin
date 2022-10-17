<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;

class InsertTaxesBraspag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dataCards = [
            [
                "name" => "Mastercard",
                "taxes" => ["2.91", "3.46", "3.66"],
                "card_flag_enum" => 3,
            ],
            [
                "name" => "Visa",
                "taxes" => ["2.91", "3.46", "3.66"],
                "card_flag_enum" => 2,
            ],
            [
                "name" => "Elo",
                "taxes" => ["2.91", "3.46", "3.71"],
                "card_flag_enum" => 4,
            ],
            [
                "name" => "American Express",
                "taxes" => ["2.91", "3.61", "3.81"],
                "card_flag_enum" => 21,
            ],
            [
                "name" => "Hipercard",
                "taxes" => ["2.91", "3.46", "3.66"],
                "card_flag_enum" => 12,
            ],
        ];

        $gatewayId = Gateway::where("name", "braspag_production")->first()->id;

        foreach ($dataCards as $card) {
            $gatewayFlag = GatewayFlag::create([
                "gateway_id" => $gatewayId,
                "name" => $card["name"],
                "slug" => strtolower(str_replace(" ", "", $card["name"])),
                "card_flag_enum" => $card["card_flag_enum"],
                "active_flag" => "1",
            ]);

            $installments = 1;
            foreach ($card["taxes"] as $key => $tax) {
                if ($key == 0) {
                    $totalInstallments = 1;
                } elseif ($key == 1) {
                    $totalInstallments = 6;
                } elseif ($key == 2) {
                    $totalInstallments = 12;
                }

                for ($i = $installments; $i <= $totalInstallments; $i++) {
                    GatewayFlagTax::create([
                        "gateway_flag_id" => $gatewayFlag->id,
                        "installments" => $installments,
                        "type_enum" => "1",
                        "percent" => $tax,
                        "active_flag" => "1",
                    ]);
                    $installments++;
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
        //
    }
}
