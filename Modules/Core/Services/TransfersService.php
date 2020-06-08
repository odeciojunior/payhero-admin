<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Transaction;

/**
 * Class TransfersService
 * @package Modules\Core\Services
 */
class TransfersService
{
    public function verifyTransactions($saleId = null)
    {
        $companyModel = new Company();
        $transferModel = new Transfer();
        $transactionModel = new Transaction();

        // Transações pagas
        if (empty($saleId)) {
            $transactions = $transactionModel->where([
                ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
            ])->whereHas('productPlanSales', function ($query) {
                    $query->whereHas('tracking');
                });
        } else {
            $transactions = $transactionModel->where([
                ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                ['sale_id', $saleId]
            ])->whereHas('productPlanSales', function ($query) {
                $query->whereHas('tracking');
                /**
                 * TODO: trocar whereDoesntHave('tracking') por:
                 *   $query->whereHas('tracking', function ($trackingsQuery) {
                 *       $trackingPresenter = (new Tracking)->present();
                 *       $status = [
                 *           $trackingPresenter->getSystemStatusEnum('valid'),
                 *           $trackingPresenter->getSystemStatusEnum('no_tracking_info'),
                 *           $trackingPresenter->getSystemStatusEnum('ignored'),
                 *           $trackingPresenter->getSystemStatusEnum('checked_manually'),
                 *       ];
                 *       $trackingsQuery->whereIn('system_status_enum', $status);
                 *   });
                 */
            });
        }

        foreach ($transactions->cursor() as $transaction) {
            try {
                $company = $companyModel->find($transaction->company_id);

                $transferModel->create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $company->user_id,
                    'company_id' => $company->id,
                    'type_enum' => $transferModel->present()->getTypeEnum('in'),
                    'value' => $transaction->value,
                    'type' => 'in',
                ]);

                $transaction->update([
                    'status' => 'transfered',
                    'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                ]);

                $company->update([
                    'balance' => intval($company->balance) + intval(preg_replace("/[^0-9]/", "", $transaction->value)),
                ]);
            } catch (Exception $e) {
                report($e);
            }
        }


        // Trasações antecipadas
        if (empty($saleId)) {
            $transactions = $transactionModel->with('anticipatedTransactions')
                ->where([
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', $transactionModel->present()->getStatusEnum('anticipated')],
                ])->whereHas('productPlanSales', function ($query) {
                    $query->whereHas('tracking');
                    /**
                     * TODO: trocar whereDoesntHave('tracking') por:
                     *   $query->whereHas('tracking', function ($trackingsQuery) {
                     *       $trackingPresenter = (new Tracking)->present();
                     *       $status = [
                     *           $trackingPresenter->getSystemStatusEnum('valid'),
                     *           $trackingPresenter->getSystemStatusEnum('no_tracking_info'),
                     *           $trackingPresenter->getSystemStatusEnum('ignored'),
                     *           $trackingPresenter->getSystemStatusEnum('checked_manually'),
                     *       ];
                     *       $trackingsQuery->whereIn('system_status_enum', $status);
                     *   });
                     */
                });
        } else {
            $transactions = $transactionModel->with('anticipatedTransactions')
                ->where([
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', $transactionModel->present()->getStatusEnum('anticipated')],
                    ['sale_id', $saleId]
                ])
                ->whereHas('productPlanSales', function ($query) {
                    $query->whereHas('tracking');
                    /**
                     * TODO: trocar whereDoesntHave('tracking') por:
                     *   $query->whereHas('tracking', function ($trackingsQuery) {
                     *       $trackingPresenter = (new Tracking)->present();
                     *       $status = [
                     *           $trackingPresenter->getSystemStatusEnum('valid'),
                     *           $trackingPresenter->getSystemStatusEnum('no_tracking_info'),
                     *           $trackingPresenter->getSystemStatusEnum('ignored'),
                     *           $trackingPresenter->getSystemStatusEnum('checked_manually'),
                     *       ];
                     *       $trackingsQuery->whereIn('system_status_enum', $status);
                     *   });
                     */
                });
        }

        foreach ($transactions->cursor() as $transaction) {
            try {
                $company = $companyModel->find($transaction->company_id);

                $transferModel->create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $company->user_id,
                    'company_id' => $company->id,
                    'type_enum' => $transferModel->present()->getTypeEnum('in'),
                    'value' => $transaction->value - $transaction->anticipatedTransactions()->first()->value,
                    'type' => 'in',
                ]);

                $transaction->update([
                    'status' => 'transfered',
                    'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                ]);

                $company->update([
                    'balance' => intval($company->balance) + intval($transaction->value - $transaction->anticipatedTransactions()->first()->value),
                ]);
            } catch (Exception $e) {
                report($e);
            }
        }
    }

}
