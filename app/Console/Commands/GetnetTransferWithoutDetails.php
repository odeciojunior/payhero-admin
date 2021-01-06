<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Services\GetNetStatementService;
use Vinkla\Hashids\Facades\Hashids;

class GetnetTransferWithoutDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:transfer-without-details';

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
        )
            ->get()
            ->pluck('id')
            ->toArray();

        $transactions = $transactionModel->with('sale')
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
            ->whereIn('gateway_id', $gatewayIds);

        foreach ($transactions->cursor() as $transaction) {

            try {
                if (!empty($transaction->company_id)) {

                    $company = $companyModel->find($transaction->company_id);

                    if (in_array($transaction->sale->gateway_id, $gatewayIds)) {

                        $subSeller = $company->subseller_getnet_id;

                        $getNetBackOfficeService = new GetnetBackOfficeService();
                        $getNetBackOfficeService->setStatementSubSellerId($subSeller)
                            ->setStatementSaleHashId(Hashids::connection('sale_id')->encode($transaction->sale_id));

                        $originalResult = $getNetBackOfficeService->getStatement();
                        $result = json_decode($originalResult);

                        $transactionsGetNet = (new GetNetStatementService())->performStatement($result);

                        if (array_key_exists('items', $transactionsGetNet)) {

                            $transactionGetNet = collect($transactionsGetNet['items'])->first();

                            if (!$transactionGetNet) {

                                $this->info(' - NOT FOUND AFTER  (new GetNetStatementService())->performStatement(...) | sale_id = ' . $transaction->sale->id);

                                if ($transaction->sale->created_at > '2020-10-30 13:28:51.0') {

                                    $orderId = $transaction->sale->hash_id . '-' . $transaction->sale->id . '-' . $transaction->sale->attempts;
                                } else {

                                    $orderId = $transaction->sale->hash_id . '-' . $transaction->sale->attempts;
                                }

                                print_r($orderId . ', ');
                                //print_r($transaction->sale->id.', ');
                                //print_r($transaction->sale->hash_id.', ');
                            } else {

                                /*if (!empty($transactionGetNet->subSellerRateConfirmDate)) {

                                    $this->info(' - UPDATE - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus() . ' :: subSellerRateConfirmDate = ' . $transactionGetNet->subSellerRateConfirmDate);

                                    $transaction->update(
                                        [
                                            'status' => 'transfered',
                                            'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                                        ]
                                    );
                                } else {

                                    $this->comment(' - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus());

                                }*/
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
