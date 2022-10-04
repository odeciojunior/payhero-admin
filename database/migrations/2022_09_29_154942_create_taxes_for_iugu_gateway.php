<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
use Modules\Core\Services\FoxUtils;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $config = [
            "token" => "A2356E05CB20C0625B95E2ED054CDBC42988F09E2AE50102FA61E658E5C13B68",
            "account_id"=>'EBF439924FA44BA5938C241607029A20'
        ];
        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (23,18,'iugu_production','{$jsonConfig}',1,1,null,NOW(),NOW());");

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (24,18,'iugu_sandbox','{$jsonConfig}',0,1,null,NOW(),NOW());");

        $flags = [
            ["gateway_id" => 23, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 23, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 23, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 23, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 23, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 23, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],

            ["gateway_id" => 24, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 24, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 24, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 24, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 24, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 24, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
        ];

        $total = count($flags);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        foreach ($flags as $flag) {
            $flagRow = GatewayFlag::create($flag);
            for ($i = 1; $i <= 12; $i++) {
                $installmentTax =2.25;
                if($i > 6){
                    $installmentTax =3.16;
                }elseif($i>1){
                    $installmentTax =2.73;
                }

                $installmentTax+= $i==1? ($i * 2.05) : ($i * 2.05)/2;

                GatewayFlagTax::create([
                    "gateway_flag_id" => $flagRow->id,
                    "installments" => $i,
                    "type_enum" => 1,
                    "percent" => round($installmentTax,2),
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
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn("iugu_balance");
        });

        DB::statement("DELETE FROM gateway_flag_taxes WHERE gateway_flag_id in (SELECT id FROM gateway_flags WHERE gateway_id in (23,24))");
        DB::statement("DELETE FROM gateway_flags WHERE gateway_id in (23,24)");
        DB::statement("DELETE FROM gateways WHERE id in (23,24)");
    }
};
