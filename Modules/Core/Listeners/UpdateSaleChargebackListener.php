<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\SaleService;
use Redis;

class UpdateSaleChargebackListener
{
    public function handle(NewChargebackEvent $event)
    {
        DB::beginTransaction();

        try {
            $sale = $event->sale;
            $sale->status = Sale::STATUS_CHARGEBACK;
            $sale->save();

            Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", true);

            SaleService::createSaleLog($sale->id, 'charge_back');

            $getnetService = new GetnetBackOfficeService();

            foreach ($sale->transactions as $transaction) {
                if (!$transaction->is_waiting_withdrawal && !empty($transaction->company_id)) {
                    $orderId = $sale->gateway_order_id;

                    $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                        ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                        ->getStatement($orderId);

                    $gatewaySale = json_decode($response);

                    if (
                        !empty($gatewaySale->list_transactions[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]) &&
                        empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                    ) {
                        $transaction->is_waiting_withdrawal = true;
                        $transaction->save();
                    }
                }

                $transaction->status = 'chargeback';
                $transaction->status_enum = Transaction::STATUS_CHARGEBACK;
                $transaction->save();
            }

            $sale_contestation = $sale->contestations()->first();

            if (!empty($sale_contestation)) {
                $sale_contestation->update([
                    'status' => SaleContestation::STATUS_LOST
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            report($e);
        }
    }
}
