<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Transaction;

/**
 * Class TransactionsService
 * @package Modules\Core\Services
 */
class TransactionsService
{

    public function verifyTransactions()
    {
        try {
            // seta false para desabilitar o pedido saque dos usuarios enquanto a rotina esta sendo executada
            settings()->group('withdrawal_request')->set('withdrawal_request', false);
        } catch (Exception $e) {
            report($e);
        }

        $transactions = Transaction::with('sale')
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

        foreach ($transactions->cursor() as $transaction) {
            try {
                if (!empty($transaction->company_id)) {
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
