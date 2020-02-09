<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Checkout;
use Modules\Core\Sms\SmsService;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\ReleasedBalanceEvent;

/**
 * Class TransfersService
 * @package Modules\Core\Services
 */
class TransfersService
{
    public function verifyTransactions()
    {

        $companyModel     = new Company();
        $transferModel    = new Transfer();
        $transactionModel = new Transaction();

        $transactions = $transactionModel->where([
                                                     ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                                                     ['status_enum', $transactionModel->present()->getStatusEnum('paid')],
                                                 ]);

        $transfers = [];

        foreach ($transactions->cursor() as $transaction) {
            try {
                $company = $companyModel->find($transaction->company_id);

                $transfer = $transferModel->create([
                                                       'transaction_id' => $transaction->id,
                                                       'user_id'        => $company->user_id,
                                                       'company_id'     => $company->id,
                                                       'type_enum'      => $transferModel->present()->getTypeEnum('in'),
                                                       'value'          => $transaction->value,
                                                       'type'           => 'in',
                                                   ]);

                $transaction->update([
                                         'status'      => 'transfered',
                                         'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                                    ]);

                $company->update([
                                     'balance' => intval($company->balance) + intval(preg_replace("/[^0-9]/", "", $transaction->value)),
                                 ]);

                $transfers[] = $transfer->toArray();
            } catch (Exception $e) {
                report($e);
            }
        }

        event(new ReleasedBalanceEvent(collect($transfers)));

        Log::info('transferencias criadas ' . print_r($transfers, true));
    }
}
