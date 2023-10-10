<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Core\Services\FoxUtils;
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
        $jsonConfig = FoxUtils::xorEncrypt(
            json_encode([
                "client_id" => "azcend_1695904311723",
                "client_secret" =>
                    "cf527b9e9a91dd413fdb908d9cf3989a33b88a3e5762df6aac970bd1fd4e37cc38b9002e595847ad8c993f3443046076",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (9,1,'suitpay_production','{$jsonConfig}',1,1,null,NOW(),NOW());");

        $jsonConfig = FoxUtils::xorEncrypt(
            json_encode([
                "client_id" => "testesandbox_1687443996536",
                "client_secret" =>
                    "5b7d6ed3407bc8c7efd45ac9d4c277004145afb96752e1252c2082d3211fe901177e09493c0d4f57b650d2b2fc1b062d",
            ])
        );
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (10,1,'suitpay_sandbox','{$jsonConfig}',0,1,null,NOW(),NOW());");

        $flags = [
            ["gateway_id" => 9, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 9, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 9],
            ["gateway_id" => 9, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 9, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 9, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 9, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
            ["gateway_id" => 9, "slug" => "boleto", "name" => "Boleto", "card_flag_enum" => 50],
            ["gateway_id" => 9, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],

            ["gateway_id" => 10, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 10, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 10, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 10],
            ["gateway_id" => 10, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 10, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 10, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
            ["gateway_id" => 10, "slug" => "boleto", "name" => "Boleto", "card_flag_enum" => 50],
            ["gateway_id" => 10, "slug" => "pix", "name" => "Pix", "card_flag_enum" => 51],
        ];

        $installmentsTax = [
            1 => 0,
            2 => 7.1,
            3 => 8.55,
            4 => 10.01,
            5 => 11.47,
            6 => 12.93,
            7 => 14.57,
            8 => 16.02,
            9 => 17.48,
            10 => 18.93,
            11 => 20.39,
            12 => 21.84,
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
                        "percent" => 4.99,
                    ]);
                    break;
                case "pix":
                    GatewayFlagTax::create([
                        "gateway_flag_id" => $gatewayFlag->id,
                        "installments" => 0,
                        "type_enum" => 3,
                        "percent" => 4.99,
                    ]);
                    break;
                default:
                    for ($i = 1; $i <= 12; $i++) {
                        GatewayFlagTax::create([
                            "gateway_flag_id" => $gatewayFlag->id,
                            "installments" => $i,
                            "type_enum" => 1,
                            "percent" => $installmentsTax[$i] + ($flag["slug"] == "elo" ? 5.99 : 4.99),
                        ]);
                    }
                    break;
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
