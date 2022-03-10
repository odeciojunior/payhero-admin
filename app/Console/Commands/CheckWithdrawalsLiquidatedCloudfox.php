<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\TransactionCloudfox;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;

class CheckWithdrawalsLiquidatedCloudfox extends Command
{
    protected $signature = 'getnet:check-withdrawals-liquidated-cloudfox';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            $transactionsCloudfox = TransactionCloudfox::with('sale', 'company')
                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->whereNotNull('gateway_released_at')->whereNull('gateway_transferred_at');

            $company = Company::find(2);

            $total = $transactionsCloudfox->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();


            $transactionsCloudfox->chunkById(100, function ($transactionsCloudfox)  use ($bar, $company){

                $getnetService = new GetnetBackOfficeService();

                foreach ($transactionsCloudfox as $transactionCloudfox) {

                    $sale = $transactionCloudfox->sale;
                    $orderId = $sale->gateway_order_id;

                    $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                        ->setStatementSubSellerId(CompanyService::getSubsellerId($company))
                        ->getStatement($orderId);

                    $gatewaySale = json_decode($response);

                    if (
                        !empty($gatewaySale->list_transactions[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                    ) {

                        $date = Carbon::parse($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date);

                        if (empty($transactionCloudfox->gateway_transferred_at)) {
                            $transactionCloudfox->update([
                                                     'status' => 'transfered',
                                                     'status_enum' => TransactionCloudfox::STATUS_TRANSFERRED,
                                                     'gateway_transferred_at' => $date
                                                 ]);
                        }
                    }
                    $bar->advance();
                }
            });

            $bar->finish();

        } catch (Exception $e) {
            report($e);
        }

    }
}
