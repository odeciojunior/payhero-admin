<?php

use Illuminate\Database\Migrations\Migration;
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
        $flags = [
            ["gateway_id" => 11, "slug" => "card", "name" => "Card", "card_flag_enum" => 1],
            ["gateway_id" => 12, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 1],
        ];

        $installmentsTax = [
            1 => 5.69,
            2 => 7.14,
            3 => 8.27,
            4 => 9.38,
            5 => 10.48,
            6 => 11.56,
            7 => 13.08,
            8 => 14.12,
            9 => 15.14,
            10 => 16.15,
            11 => 17.14,
            12 => 18.11,
        ];

        $total = count($flags);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

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
            break;

            $progress->advance();
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
