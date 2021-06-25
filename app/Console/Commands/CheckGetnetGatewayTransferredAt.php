<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PixTransfer;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Vinkla\Hashids\Facades\Hashids;

class CheckGetnetGatewayTransferredAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:getnet_gateway_transferred_at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $transactionModel = new Transaction();
        $getnetService = new GetnetBackOfficeService();

        $transactionsCount = $transactionModel->with('sale')
            ->whereNotNull('withdrawal_id')
//            ->where('withdrawal_id', 12024)
            ->whereNull('gateway_transferred_at')
            ->whereIn('gateway_id', [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID, Gateway::GERENCIANET_PRODUCTION_ID])
            ->count();

        $transactions = $transactionModel->with('sale')
            ->whereNotNull('withdrawal_id')
//            ->where('withdrawal_id', 12024)
            ->whereNull('gateway_transferred_at')
            ->whereIn('gateway_id', [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID, Gateway::GERENCIANET_PRODUCTION_ID])
            ->orderBy('id', 'desc');

        $transactions->chunk(200, function ($transactions) use ($getnetService, $transactionsCount) {
            $i = 0;
            foreach ($transactions as $transaction) {
                    if ( $i > 5) {
                        sleep(1);
                        $i = 0;
                    }
                try {
                    $this->line($transactionsCount . ' Atualizando a transação: ' . $transaction->id . "Count: ". $i );

                    if (empty($transaction->company_id)) {
                        continue;
                    }
                    $sale = $transaction->sale;
                    $saleIdEncoded = Hashids::connection('sale_id')->encode($sale->id);

                    if ($transaction->gateway_id == Gateway::GERENCIANET_PRODUCTION_ID) {

                        if($transaction->gateway_transferred === 1) {
                            $transaction->update([
                                'gateway_transferred_at' => $transaction->gateway_released_at //date transferred
                            ]);
                        }

                    }
                    else {
                        $i ++;
                        if (FoxUtils::isProduction()) {
                            $subsellerId = $transaction->company->subseller_getnet_id;
                        } else {
                            $subsellerId = $transaction->company->subseller_getnet_homolog_id;
                        }

                        $getnetService->setStatementSubSellerId($subsellerId)
                            ->setStatementSaleHashId($saleIdEncoded);

                        $result = json_decode($getnetService->getStatement());

                        if (!empty($result->list_transactions[0]) &&
                            !empty($result->list_transactions[0]->details[0]) &&
                            !empty($result->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                        ) {


                            $date = Carbon::parse($result->list_transactions[0]->details[0]->subseller_rate_confirm_date);


                            $transaction->update([
                                 'gateway_transferred_at' => $date, //date transferred
                                 'gateway_transferred' => 1
                             ]);


                        }
                    }

                } catch (Exception $e) {
                    report($e);
                }
            }
        });
    }
}
