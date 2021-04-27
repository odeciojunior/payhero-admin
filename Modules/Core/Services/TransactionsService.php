<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class TransactionsService
 * @package Modules\Core\Services
 */
class TransactionsService
{

    public function verifyGetnetTransactions()
    {
        try {
            // seta false para desabilitar o pedido saque dos usuarios enquanto a rotina esta sendo executada
            settings()->group('withdrawal_request')->set('withdrawal_request', false);
        } catch (Exception $e) {
            report($e);
        }

        $transactionModel = new Transaction();

        $transactions = $transactionModel->with('sale')
            ->where(
                [
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', (new Transaction())->present()->getStatusEnum('paid')],
                    ['is_waiting_withdrawal', 0],
                ]
            )->whereHas(
                'sale',
                function ($query) {
                    $query->where(
                        function ($q) {
                            $q->where('has_valid_tracking', true)
                                ->orWhereNull('delivery_id');
                        }
                    )->whereIn('gateway_id', [14, 15]);
                }
            );

        $getnetService = new GetnetBackOfficeService();

        foreach ($transactions->cursor() as $transaction) {
            try {

                if (empty($transaction->company_id)) {
                    continue;
                }
                $sale = $transaction->sale;
                $saleIdEncoded = Hashids::connection('sale_id')->encode($sale->id);

                if (FoxUtils::isProduction()) {
                    $subsellerId = $transaction->company->subseller_getnet_id;
                } else {
                    $subsellerId = $transaction->company->subseller_getnet_homolog_id;
                }

                $getnetService->setStatementSubSellerId($subsellerId)
                    ->setStatementSaleHashId($saleIdEncoded);

                $result = json_decode($getnetService->getStatement());

                if (!empty($result->list_transactions) &&
                    !is_null($result->list_transactions[0]) &&
                    !is_null($result->list_transactions[0]->details[0]) &&
                    !is_null($result->list_transactions[0]->details[0]->release_status)
                    && $result->list_transactions[0]->details[0]->release_status == 'N'
                ) {
                    $transaction->update(
                        [
                            'is_waiting_withdrawal' => 1,
                        ]
                    );
                }
            } catch (Exception $e) {
                report($e);
            }
        }

        try {
            settings()->group('withdrawal_request')->set('withdrawal_request', true);
        } catch (Exception $e) {
            report($e);
        }
    }
}
