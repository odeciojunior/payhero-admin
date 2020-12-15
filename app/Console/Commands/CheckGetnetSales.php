<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Getnet\Details;
use Modules\Transfers\Services\GetNetStatementService;
use Vinkla\Hashids\Facades\Hashids;

class CheckGetnetSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:getnet';

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


        $transactions = Transaction::where('status_enum', (new Transaction)->present()->getStatusEnum('paid'))
                    ->whereHas('sale', function($q){
                        $q->where('has_valid_tracking', true);
                        $q->whereIn('gateway_id', [14, 15]);
                    })
                    ->where('release_date', '<=', now())
                    ->whereNotNull('company_id')
                    ->orderBy('id', 'desc');

        $getNetBackOfficeService = new GetnetBackOfficeService();

        foreach($transactions->cursor() as $transaction) {

            $getNetBackOfficeService->setStatementSubSellerId($transaction->company->subseller_getnet_id)
                ->setStatementStartDate(Carbon::now()->subYears(2))
                ->setStatementEndDate(Carbon::now()->addYear())
                ->setStatementDateField('transaction')
                ->setStatementSaleHashId(Hashids::connection('sale_id')->encode($transaction->sale_id));

            /*$result = $getNetBackOfficeService->getStatement();

            $statement = (new GetNetStatementService())->performStatement(json_decode($result));
            $statement = collect($statement);

            if($statement->transactions->first()->identify == GetNetStatementService::SEARCH_STATUS_WAITING_WITHDRAWAL) {
                $transaction->update(
                    [
                        'status' => 'waiting_withdrawal',
                        'status_enum' => (new Transaction)->present()->getStatuEnum('waiting_withdrawal')
                    ]
                );
            }

            Log::info($statement->toArray());*/

            $result = $getNetBackOfficeService->getStatement();
            $result = json_decode($result);

            $transactionsGetNet = (new GetNetStatementService())->performStatement($result);

            if (array_key_exists('items', $transactionsGetNet)) {

                $transactionGetNet = collect($transactionsGetNet['items'])->first();

                if (!$transactionGetNet) {

                    $this->info(' - NOT FOUND AFTER  (new GetNetStatementService())->performStatement(...) | sale_id = ' . $transaction->sale->id);
                } else {

                    if ($transactionGetNet->details->getType() == Details::STATUS_WAITING_WITHDRAWAL) {

                        $this->info(' - UPDATE - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus());

                        /*$transaction->update(
                            [
                                'status' => 'waiting_withdrawal',
                                'status_enum' => $transactionModel->present()->getStatusEnum('waiting_withdrawal'),
                            ]
                        );*/
                    } else {

                        $this->comment(' - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus());

                    }
                }
            }

        }

        dd($transactions);
    }
}
