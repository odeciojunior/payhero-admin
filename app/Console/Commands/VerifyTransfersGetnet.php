<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Services\GetNetStatementService;
use Vinkla\Hashids\Facades\Hashids;

class VerifyTransfersGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:transfersgetnet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'routine responsible for transferring the available money from the transactions to the users company registered getnet account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $companyModel = new Company();
        $transactionModel = new Transaction();

        $gatewayIds = Gateway::whereIn(
            'name',
            [
                'getnet_sandbox',
                'getnet_production'
            ]
        )->get()->pluck('id')->toArray();

        $transactions = $transactionModel->with('sale')
            //->where('transactions.id', 2106552)
            ->where('release_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
            ->whereHas(
                'sale',
                function ($query) use ($gatewayIds) {
                    $query->where(
                        function ($q) use ($gatewayIds) {
                            $q->where('has_valid_tracking', true)->orWhereNull('delivery_id');
                        }
                    )->whereIn('gateway_id', $gatewayIds);
                }
            );


        foreach ($transactions->cursor() as $transaction) {

            try {
                if (!empty($transaction->company_id)) {

                    $company = $companyModel->find($transaction->company_id);

                    if (in_array($transaction->sale->gateway_id, $gatewayIds)) {

                        $subSeller = $company->subseller_getnet_id;
                        $startDate = Carbon::createFromFormat('Y-m-d', '2020-07-01');
                        $endDate = today();
                        $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_TRANSACTION;

                        $getNetBackOfficeService = new GetnetBackOfficeService();
                        $getNetBackOfficeService->setStatementSubSellerId($subSeller)
                            ->setStatementStartDate($startDate)
                            ->setStatementEndDate($endDate)
                            ->setStatementDateField($statementDateField)
                            ->setStatementSaleHashId(Hashids::connection('sale_id')->encode($transaction->sale_id));

                        $originalResult = $getNetBackOfficeService->getStatement();
                        $result = json_decode($originalResult);

                        $transactionsGetNet = (new GetNetStatementService())->performStatement($result);

                        if (array_key_exists('items', $transactionsGetNet)) {

                            $transactionGetNet = collect($transactionsGetNet['items'])->first();

                            if (!$transactionGetNet) {

                                //dd($transactionsGetNet, $originalResult, $transaction->toArray(), $transaction->sale->hash_id);

                                if ($transaction->sale->created_at > '2020-10-30 13:28:51.0') {

                                    $orderId = $transaction->sale->hash_id . '-' . $transaction->sale->id . '-' . $transaction->sale->attempts;
                                } else {

                                    $orderId = $transaction->sale->hash_id . '-' . $transaction->sale->attempts;
                                }

                                print_r($orderId . ', ');
                                //print_r($transaction->sale->id.', ');
                                //print_r($transaction->sale->hash_id.', ');
                            }

                            //$this->info(' - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus());
                            #$this->info(' # ' . $transactionGetNet);

                            if (!empty($transactionGetNet->subSellerRateConfirmDate)) {

                                #$this->info('   ### ' . $transactionGetNet->details->getStatus());
                                /*$transaction->update(
                                    [
                                        'status' => 'transfered',
                                        'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                                    ]
                                );*/
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
