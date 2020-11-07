<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Transfers\Services\GetNetStatementService;
use Storage;
use Vinkla\Hashids\Facades\Hashids;

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

        $fileLogName = 'TransfersService_' . date('Ymd') . '.log';

        $companyModel = new Company();
        $transferModel = new Transfer();
        $transactionModel = new Transaction();

        try {
            // seta false para desabilitar o pedido saque dos usuarios enquanto a rotina esta sendo executada
            settings()->group('withdrawal_request')->set('withdrawal_request', false);
        } catch (Exception $e) {
            report($e);
        }

        $gatewayIds = Gateway::whereIn('name', [
            'getnet_sandbox',
            'getnet_production'
        ])
            ->get()->pluck('id')->toArray();

        // Transações pagas
        if (empty($saleId)) {

            $transactions = $transactionModel->with('sale')
                ->where([
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                ])->whereHas('sale', function ($query) {
                    $query->where('has_valid_tracking', true)
                        ->orWhereNull('delivery_id');
                });
        } else {
            $transactions = $transactionModel->with('sale')
                ->where([
                    ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                    ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                    ['sale_id', $saleId]
                ])->whereHas('sale', function ($query) {
                    $query->where('has_valid_tracking', true)
                        ->orWhereNull('delivery_id');
                });
        }

        foreach ($transactions->cursor() as $transaction) {
            try {
                if (!empty($transaction->company_id)) {
                    $company = $companyModel->find($transaction->company_id);

                    if (!in_array($transaction->sale->gateway_id, $gatewayIds)) {

                        $transferModel->create([
                            'transaction_id' => $transaction->id,
                            'user_id' => $company->user_id,
                            'company_id' => $company->id,
                            'type_enum' => $transferModel->present()->getTypeEnum('in'),
                            'value' => $transaction->value,
                            'type' => 'in',
                        ]);

                        $company->update([
                            'balance' => intval($company->balance) + intval(preg_replace("/[^0-9]/", "",
                                    $transaction->value)),
                        ]);

                        $transaction->update([
                            'status' => 'transfered',
                            'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                        ]);
                    } else {

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

                        $result = $getNetBackOfficeService->getStatement();
                        $result = json_decode($result);

                        $transactionsGetNet = (new GetNetStatementService())->performStatement($result);
                        $transactionGetNet = collect($transactionsGetNet)->first();

                        // Para Debug
                        // echo "\r\n - " . $transaction->sale_id . ' - orderId: ' . $transactionGetNet->orderId . ' # subSellerRateConfirmDate = ' . $transactionGetNet->subSellerRateConfirmDate;

                        if (!empty($transactionGetNet->subSellerRateConfirmDate)) {

                            /*Storage::disk('local')->append($fileLogName, json_encode([
                                'id' => $transaction->id,
                                'status' => $transaction->status,
                                'status_enum' => $transaction->status_enum,
                                'transactionGetNet' => (array)$transactionGetNet,
                            ]));*/

                            $transaction->update([
                                'status' => 'transfered',
                                'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                            ]);
                        }
                    }

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
