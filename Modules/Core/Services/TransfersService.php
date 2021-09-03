<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use PDOException;

/**
 * Class TransfersService
 * @package Modules\Core\Services
 */
class TransfersService
{

    /**
     * @param null $saleId
     */
    public function verifyTransactions($saleId = null)
    {
        try {
            // seta false para desabilitar o pedido saque dos usuarios enquanto a rotina esta sendo executada
            settings()->group('withdrawal_request')->set('withdrawal_request', false);
        } catch (Exception $e) {
            report($e);
        }

        $gatewayIds = Gateway::whereIn('name', ['getnet_sandbox', 'getnet_production'])
            ->get()
            ->pluck('id')
            ->toArray();

        $transactions = Transaction::with('company')
            ->where([
                ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                ['status_enum', Transaction::STATUS_PAID],
            ])->where(function ($where) use ($gatewayIds) {
                $where->where('tracking_required', false)
                    ->orWhereHas('sale', function ($query) use ($gatewayIds) {
                        $query->where(function ($q) {
                            $q->where('has_valid_tracking', true)
                                ->orWhereNull('delivery_id');
                        })->whereNotIn('gateway_id', $gatewayIds);
                    });
            });

        if (empty($saleId)) {
            $transactions->where('sale_id', $saleId);
        }

        dd($transactions->count());

        try {
            DB::beginTransaction();
            foreach ($transactions->cursor() as $transaction) {
                try {
                    if (!empty($transaction->company_id)) {
                        $company = $transaction->company;

                        if (!in_array($transaction->sale->gateway_id, $gatewayIds)) {
                            Transfer::create(
                                [
                                    'transaction_id' => $transaction->id,
                                    'user_id' => $company->user_id,
                                    'company_id' => $company->id,
                                    'type_enum' => (new Transfer)->present()->getTypeEnum('in'),
                                    'value' => $transaction->value,
                                    'type' => 'in',
                                ]
                            );

                            $company->update([
                                'balance' => $company->balance +  $transaction->value
                            ]);

                            $transaction->update([
                                    'status' => 'transfered',
                                    'status_enum' => (new Transaction)->present()->getStatusEnum('transfered'),
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    report($e);
                }
            }
            DB::commit();
        } catch (PDOException $e) {
            DB::rollBack();
            report($e);
        }

        try {
            settings()->group('withdrawal_request')->set('withdrawal_request', true);
        } catch (Exception $e) {
            report($e);
        }
    }
}
