<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `customer_cards`
	    CHANGE COLUMN `first_six_digits` `first_six_digits` INT(10) NULL AFTER `customer_id`;");

        $taxes = [
            1 => 4.91,
            2 => 5.9,
            3 => 6.94,
            4 => 7.98,
            5 => 10.02,
            6 => 12.58,
            7 => 13.67,
            8 => 14.75,
            9 => 15.82,
            10 => 15.82,
            11 => 16.89,
            12 => 17.94,
        ];

        $flags = GatewayFlag::whereIn("gateway_id", [Gateway::ABMEX_PRODUCTION_ID, Gateway::ABMEX_SANDBOX_ID])->get();
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
