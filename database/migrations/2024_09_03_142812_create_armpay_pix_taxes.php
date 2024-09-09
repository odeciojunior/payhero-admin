<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gateways = [
            [
                "id" => 21,
                
            ],
            [
                "id" => 22,
            ],
        ];

        foreach ($gateways as $gateway) {
            $gatewayFlag = GatewayFlag::create([
                "gateway_id" => $gateway["id"],
                "slug" => "pix",
                "name" => "Pix",
                "card_flag_enum" => "51",
            ]);

                GatewayFlagTax::create([
                    "gateway_flag_id" => $gatewayFlag->id,
                    "installments" => 1,
                    "type_enum" => 1,
                    "percent" => 1.00,
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
        GatewayFlagTax::whereIn('gateway_flag_id', function ($query) {
            $query->select('id')
            ->from('gateway_flags')
            ->whereIn('gateway_id', [21, 22]);
        })->delete();
        GatewayFlag::whereIn('gateway_id', [21, 22])->delete();
    }
};
