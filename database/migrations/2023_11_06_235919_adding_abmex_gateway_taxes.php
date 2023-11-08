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
            ["gateway_id" => 11, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 11, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 11],
            ["gateway_id" => 11, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 11, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 11, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 11, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
            ["gateway_id" => 11, "slug" => "boleto", "name" => "Boleto", "card_flag_enum" => 50],
            ["gateway_id" => 11, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],

            ["gateway_id" => 12, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 12, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 12, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 12],
            ["gateway_id" => 12, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 12, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 12, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
            ["gateway_id" => 12, "slug" => "boleto", "name" => "Boleto", "card_flag_enum" => 50],
            ["gateway_id" => 12, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],
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

            switch ($flag["slug"]) {
                case "boleto":
                    GatewayFlagTax::create([
                        "gateway_flag_id" => $gatewayFlag->id,
                        "installments" => 0,
                        "type_enum" => 2,
                        "percent" => 2.49,
                    ]);
                    break;
                case "pix":
                    GatewayFlagTax::create([
                        "gateway_flag_id" => $gatewayFlag->id,
                        "installments" => 0,
                        "type_enum" => 3,
                        "percent" => 1.0,
                    ]);
                    break;
                default:
                    for ($i = 1; $i <= 12; $i++) {
                        GatewayFlagTax::create([
                            "gateway_flag_id" => $gatewayFlag->id,
                            "installments" => $i,
                            "type_enum" => 1,
                            "percent" => $installmentsTax[$i],
                        ]);
                    }
                    break;
            }

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
