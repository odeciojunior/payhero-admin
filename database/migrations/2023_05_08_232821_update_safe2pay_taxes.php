<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
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
        $taxes = [
            1 => 1.95,
            2 => 2.91,
            3 => 3.88,
            4 => 4.86,
            5 => 5.83,
            6 => 6.8,
            7 => 7.73,
            8 => 8.69,
            9 => 9.66,
            10 => 10.63,
            11 => 11.59,
            12 => 12.56,
        ];

        $flags = GatewayFlag::whereIn("gateway_id", [
            Gateway::SAFE2PAY_PRODUCTION_ID,
            Gateway::SAFE2PAY_SANDBOX_ID,
        ])->get();
        foreach ($flags as $flag) {
            $installments = GatewayFlagTax::where("gateway_flag_id", $flag->id)->get();
            foreach ($installments as $item) {
                $item->update([
                    "percent" => $taxes[$item->installments],
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
