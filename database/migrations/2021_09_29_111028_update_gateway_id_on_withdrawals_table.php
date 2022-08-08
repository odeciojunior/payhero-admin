<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Withdrawal;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Modules\Core\Entities\Gateway;

class UpdateGatewayIdOnWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $withdrawals = Withdrawal::get();
        $total = count($withdrawals);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        foreach ($withdrawals as $withdrawal) {
            if ($withdrawal->automatic_liquidation) {
                $withdrawal->update([
                    "gateway_id" => Gateway::GETNET_PRODUCTION_ID,
                ]);
            } else {
                $withdrawal->update([
                    "gateway_id" => Gateway::CIELO_PRODUCTION_ID,
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
}
