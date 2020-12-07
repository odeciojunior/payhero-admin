<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
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

            $result = $getNetBackOfficeService->getStatement();

            $statement = (new GetNetStatementService())->performStatement(json_decode($result));
            $statement = collect($statement);

            // if($statement->transactions->first()->identify == GetNetStatementService::SEARCH_STATUS_WAITING_WITHDRAWAL) {
            //     $transaction->update(
            //         [
            //             'status' => 'waiting_withdrawal',
            //             'status_enum' => (new Transaction)->present()->getStatuEnum('waiting_withdrawal')
            //         ]
            //     );
            // }

            Log::info($statement->toArray());
            
        }

        // $transactions = (new GetNetStatementService())->performStatement($result);
        // $transactions = collect($transactions);

        dd($transactions);
    }
}
