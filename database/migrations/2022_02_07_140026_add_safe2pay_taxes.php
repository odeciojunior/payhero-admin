<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddSafe2payTaxes extends Migration
{

    public function up()
    {
        $flags = [
            ['gateway_id' => 21,'slug' => 'visa','name' => 'Visa','card_flag_enum' => 2],
            ['gateway_id' => 21,'slug' => 'mastercard','name' => 'Master Card','card_flag_enum' => 3],
            ['gateway_id' => 21,'slug' => 'elo','name' => 'Elo','card_flag_enum' => 4],
            ['gateway_id' => 21,'slug' => 'amex','name' => 'AMEX','card_flag_enum' => 5],
            ['gateway_id' => 21,'slug' => 'discover','name' => 'Discover','card_flag_enum' => 22],
            ['gateway_id' => 21,'slug' => 'hipercard','name' => 'Hipercard','card_flag_enum' => 12],
            
            ['gateway_id' => 22,'slug' => 'visa','name' => 'Visa','card_flag_enum' => 2],
            ['gateway_id' => 22,'slug' => 'mastercard','name' => 'Master Card','card_flag_enum' => 3],
            ['gateway_id' => 22,'slug' => 'elo','name' => 'Elo','card_flag_enum' => 4],
            ['gateway_id' => 22,'slug' => 'amex','name' => 'AMEX','card_flag_enum' => 5],
            ['gateway_id' => 22,'slug' => 'discover','name' => 'Discover','card_flag_enum' => 22],
            ['gateway_id' => 22,'slug' => 'hipercard','name' => 'Hipercard','card_flag_enum' => 12],            
        ];

        $installmentsTax = [
            1 => 2.99,
            2 => 4.05,
            3 => 4.61,
            4 => 5.17,
            5 => 5.72,
            6 => 6.27,
            7 => 7.29,
            8 => 7.85,
            9 => 8.37,
            10 => 8.9,
            11 => 9.42,
            12 => 9.95,
        ];
        
        $total = count($flags);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        foreach($flags as $flag){

            $gatewayFlag = GatewayFlag::create($flag);

            for ($i=1; $i <=12 ; $i++) {
                GatewayFlagTax::create([
                    'gateway_flag_id' => $gatewayFlag->id,
                    'installments' => $i,
                    'type_enum' => 1,
                    'percent' => $installmentsTax[$i]
                ]);
            }

            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
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
}
