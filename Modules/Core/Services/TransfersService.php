<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Gateway;

/**
 * Class TransfersService
 * @package Modules\Core\Services
 */
class TransfersService
{
    /**
     * @param  null  $saleId
     * @throws PresenterException
     */
    public function verifyTransactions($saleId = null)
    {
        $companyModel = new Company();
        $transferModel = new Transfer();
        $transactionModel = new Transaction();

        try{
            // seta false para desabilitar o pedido saque dos usuarios enquanto a rotina esta sendo executada
            settings()->group('withdrawal_request')->set('withdrawal_request', false);
        }catch (Exception $e){
            report($e);
        }

        $gatewayIds = Gateway::whereIn('name', ['getnet_sandbox','getnet_production','braspag_sandbox','braspag_production'])
            ->get()->pluck('id')->toArray();

        // Transações pagas
        if (empty($saleId)) {
            $transactions = $transactionModel->with('sale')
            ->where([
                ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
            ])->whereHas('productPlanSales', function ($query) {
                $query->whereHas('tracking', function ($trackingsQuery) {
                    $trackingPresenter = (new Tracking())->present();
                    $status = [
                        $trackingPresenter->getSystemStatusEnum('valid'),
                        $trackingPresenter->getSystemStatusEnum('ignored'),
                        $trackingPresenter->getSystemStatusEnum('checked_manually'),
                    ];
                    $trackingsQuery->whereIn('system_status_enum', $status);
                });
            });
        } else {
            $transactions = $transactionModel->with('sale')
            ->where([
                ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                ['sale_id', $saleId]
            ])->whereHas('productPlanSales', function ($query) {
                $query->whereHas('tracking', function ($trackingsQuery) {
                    $trackingPresenter = (new Tracking())->present();
                    $status = [
                        $trackingPresenter->getSystemStatusEnum('valid'),
                        $trackingPresenter->getSystemStatusEnum('ignored'),
                        $trackingPresenter->getSystemStatusEnum('checked_manually'),
                    ];
                    $trackingsQuery->whereIn('system_status_enum', $status);
                });
            });
        }

        foreach ($transactions->cursor() as $transaction) {
            try {
                if(!empty($transaction->company_id)){

                    $company = $companyModel->find($transaction->company_id);

                    if(!in_array($transaction->sale->gateway_id, $gatewayIds)) {

                        $transferModel->create([
                            'transaction_id' => $transaction->id,
                            'user_id' => $company->user_id,
                            'company_id' => $company->id,
                            'type_enum' => $transferModel->present()->getTypeEnum('in'),
                            'value' => $transaction->value,
                            'type' => 'in',
                        ]);

                        $company->update([
                            'balance' => intval($company->balance) + intval(preg_replace("/[^0-9]/", "", $transaction->value)),
                        ]);
                    }

                    $transaction->update([
                        'status' => 'transfered',
                        'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                    ]);

                }
            } catch (Exception $e) {
                report($e);
            }
        }

        // Trasações antecipadas
        if (empty($saleId)) {
            $transactions = $transactionModel->with('anticipatedTransactions','sale')
                ->where([
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', $transactionModel->present()->getStatusEnum('anticipated')],
                ])->whereHas('productPlanSales', function ($query) {
                    $query->whereHas('tracking', function ($trackingsQuery) {
                        $trackingPresenter = (new Tracking())->present();
                        $status = [
                            $trackingPresenter->getSystemStatusEnum('valid'),
                            $trackingPresenter->getSystemStatusEnum('ignored'),
                            $trackingPresenter->getSystemStatusEnum('checked_manually'),
                        ];
                        $trackingsQuery->whereIn('system_status_enum', $status);
                    });
                });
        } else {
            $transactions = $transactionModel->with('anticipatedTransactions','sale')
                ->where([
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', $transactionModel->present()->getStatusEnum('anticipated')],
                    ['sale_id', $saleId]
                ])
                ->whereHas('productPlanSales', function ($query) {
                    $query->whereHas('tracking', function ($trackingsQuery) {
                        $trackingPresenter = (new Tracking())->present();
                        $status = [
                            $trackingPresenter->getSystemStatusEnum('valid'),
                            $trackingPresenter->getSystemStatusEnum('ignored'),
                            $trackingPresenter->getSystemStatusEnum('checked_manually'),
                        ];
                        $trackingsQuery->whereIn('system_status_enum', $status);
                    });
                });
        }

        foreach ($transactions->cursor() as $transaction) {
            try {
                if(!empty($transaction->company_id)){

                    $company = $companyModel->find($transaction->company_id);

                    if(!in_array($transaction->sale->gateway_id, $gatewayIds)) {
                        $transferModel->create([
                            'transaction_id' => $transaction->id,
                            'user_id' => $company->user_id,
                            'company_id' => $company->id,
                            'type_enum' => $transferModel->present()->getTypeEnum('in'),
                            'value' => $transaction->value - $transaction->anticipatedTransactions()->first()->value,
                            'type' => 'in',
                        ]);

                        $company->update([
                            'balance' => intval($company->balance) + intval($transaction->value - $transaction->anticipatedTransactions()->first()->value),
                        ]);
                    }

                    $transaction->update([
                        'status' => 'transfered',
                        'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                    ]);

                }
            } catch (Exception $e) {
                report($e);
            }
        }

        try {
            settings()->group('withdrawal_request')->set('withdrawal_request', true);
        }catch (Exception $e){
            report($e);
        }
    }
}
