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
            1 => "4.30",
            2 => "5.90",
            3 => "6.96",
            4 => "8.01",
            5 => "9.05",
            6 => "10.06",
            7 => "11.44",
            8 => "12.42",
            9 => "13.40",
            10 => "14.35",
            11 => "15.30",
            12 => "16.23",
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
