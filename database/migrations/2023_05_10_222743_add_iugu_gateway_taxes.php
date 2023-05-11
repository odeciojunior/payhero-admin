<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
            ["gateway_id" => 7, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 7, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 7],
            ["gateway_id" => 7, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 7, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 7, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 7, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],

            ["gateway_id" => 8, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 8, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 8, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 8],
            ["gateway_id" => 8, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 8, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 8, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
            ["slug" => "card", "name" => "Card", "card_flag_enum" => 1],
        ];

        $installmentsTax = [
            1 => "3.10",
            2 => "4.43",
            3 => "5.32",
            4 => "6.20",
            5 => "7.07",
            6 => "7.93",
            7 => "9.07",
            8 => "9.90",
            9 => "10.74",
            10 => "11.55",
            11 => "12.36",
            12 => "13.16",
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

            $progress->advance();
        }

        $progress->finish();
        $output->writeln("");
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
