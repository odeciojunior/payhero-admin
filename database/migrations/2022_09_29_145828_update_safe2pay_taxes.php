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

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gatewayFlags = DB::table("gateway_flags")->whereIn('gateway_id',[Gateway::SAFE2PAY_PRODUCTION_ID,Gateway::SAFE2PAY_SANDBOX_ID])->get();
        $total = count($gatewayFlags);

        $taxes = [
            4.81,
            6.42,
            7.53,
            8.67,
            9.79,
            10.91,
            12.49,
            13.61,
            14.73,
            15.85,
            16.96,
            18.08
        ];

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        foreach ($gatewayFlags as $flag)
        {
            for ($i = 1; $i <= 12; $i++) {
                $taxFlag = GatewayFlagTax::where('gateway_flag_id',$flag->id)->where('installments',$i)->where('type_enum',1)->first();
                if(!empty($taxFlag)){
                    $taxFlag->update([
                        "percent" =>$taxes[$i-1]
                    ]);
                }
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
