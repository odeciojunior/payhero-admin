<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class NewFlagsForGatewayAsaas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $flags = [
            ["gateway_id" => 8, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 8, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 8, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 8, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 8, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 8, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],

            ["gateway_id" => 20, "slug" => "visa", "name" => "Visa", "card_flag_enum" => 2],
            ["gateway_id" => 20, "slug" => "mastercard", "name" => "Master Card", "card_flag_enum" => 3],
            ["gateway_id" => 20, "slug" => "elo", "name" => "Elo", "card_flag_enum" => 4],
            ["gateway_id" => 20, "slug" => "amex", "name" => "AMEX", "card_flag_enum" => 5],
            ["gateway_id" => 20, "slug" => "discover", "name" => "Discover", "card_flag_enum" => 22],
            ["gateway_id" => 20, "slug" => "hipercard", "name" => "Hipercard", "card_flag_enum" => 12],
        ];

        $total = count($flags);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        foreach ($flags as $flag) {
            $flagRow = GatewayFlag::create($flag);
            for ($i = 1; $i <= 12; $i++) {
                $installmentTax = ($i == 1 ? 1.7 : 1.8) + ($i + 1) / 2;
                GatewayFlagTax::create([
                    "gateway_flag_id" => $flagRow->id,
                    "installments" => $i,
                    "type_enum" => 1,
                    "percent" => $installmentTax,
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
        $flags = GatewayFlag::where("gateway_id", 8)->get();
        foreach ($flags as $flag) {
            DB::statement("DELETE FROM gateway_flag_taxes WHERE gateway_flag_id = {$flag->id}");
            $flag->delete();
        }
    }
}
