<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use PDOException;
use DB;

/**
 * Class TransfersService
 * @package Modules\Core\Services
 */
class TransfersService
{

    /**
     * @param null $saleId
     * @throws PresenterException
     */
    public function verifyTransactions($saleId = null)
    {
        $companyModel = new Company();
        $transferModel = new Transfer();
        $transactionModel = new Transaction();

        try {
            // seta false para desabilitar o pedido saque dos usuarios enquanto a rotina esta sendo executada
            settings()->group('withdrawal_request')->set('withdrawal_request', false);
        } catch (Exception $e) {
            report($e);
        }

        $gatewayIds = Gateway::whereIn(
            'name',
            [
                'getnet_sandbox',
                'getnet_production'
            ]
        )->get()->pluck('id')->toArray();

        // Transações pagas
        if (empty($saleId)) {
            $transactions = $transactionModel->with('sale')
                ->where(
                    [
                        ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                        ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                    ]
                )->whereHas(
                    'sale',
                    function ($query) use ($gatewayIds) {
                        $query->where(
                            function ($q) {
                                $q->where('has_valid_tracking', true)
                                    ->orWhereNull('delivery_id');
                            }
                        )->whereNotIn('gateway_id', $gatewayIds);
                    }
                );
        } else {
            $transactions = $transactionModel->with('sale')
                ->where(
                    [
                        ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                        ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                        ['sale_id', $saleId]
                    ]
                )->whereHas(
                    'sale',
                    function ($query) use ($gatewayIds) {
                        $query->where(
                            function ($q) {
                                $q->where('has_valid_tracking', true)
                                    ->orWhereNull('delivery_id');
                            }
                        )->whereNotIn('gateway_id', $gatewayIds);
                    }
                );
        }

        try {
            DB::beginTransaction();
            foreach ($transactions->cursor() as $transaction) {
                try {
                    if (!empty($transaction->company_id)) {
                        $company = $companyModel->find($transaction->company_id);

                        if (!in_array($transaction->sale->gateway_id, $gatewayIds)) {
                            $transferModel->create(
                                [
                                    'transaction_id' => $transaction->id,
                                    'user_id' => $company->user_id,
                                    'company_id' => $company->id,
                                    'type_enum' => $transferModel->present()->getTypeEnum('in'),
                                    'value' => $transaction->value,
                                    'type' => 'in',
                                ]
                            );

                            $company->update(
                                [
                                    'balance' => intval($company->balance) + intval(
                                            preg_replace(
                                                "/[^0-9]/",
                                                "",
                                                $transaction->value
                                            )
                                        ),
                                ]
                            );

                            $transaction->update(
                                [
                                    'status' => 'transfered',
                                    'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                                ]
                            );
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
