<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class CreateGerencianetTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allTransactions = Transaction::with('company')
                                        ->whereIn('gateway_id',[
                                            Gateway::GERENCIANET_PRODUCTION_ID, 
                                            Gateway::GERENCIANET_SANDBOX_ID
                                        ])
                                        ->where('is_waiting_withdrawal', 1)
                                        ->whereNull('withdrawal_id')
                                        ->whereNotNull('company_id');

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $allTransactions->count());
        $progress->start();

        $allTransactions->chunkById(
            1000,
            function ($transactions) use ($progress) {
                foreach($transactions as $transaction) {

                    Transfer::create(
                        [
                            'transaction_id' => $transaction->id,
                            'user_id' => $transaction->company->user_id,
                            'company_id' => $transaction->company->id,
                            'type_enum' => Transfer::TYPE_IN,
                            'value' => $transaction->value,
                            'type' => 'in',
                            'gateway_id' => Gateway::GERENCIANET_PRODUCTION_ID
                        ]
                    );

                    $progress->advance();
                }

            }
        );

        $progress->finish();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gerencianet_transfers');
    }
}
