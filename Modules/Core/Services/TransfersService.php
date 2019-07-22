<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use App\Entities\Company;
use App\Entities\Transfer;
use App\Entities\Transaction;
use Modules\Core\Sms\SmsService;
use Illuminate\Support\Facades\Log;

class TransfersService
{
    public function verifyTransactions() {

        $transactions = Transaction::where([
                                               ['release_date', '<=', Carbon::now()->format('Y-m-d')],
                                               ['status', 'paid'],
                                           ])->get();

        $transfers = [];

        $companyModel  = new Company();
        $transferModel = new Transfer();

        foreach ($transactions as $transaction) {
            try {
                $company = $companyModel->find($transaction->company);

                $transfer = $transferModel->create([
                                                 'transaction' => $transaction->id,
                                                 'user'        => $company->user_id,
                                                 'value'       => $transaction->value,
                                                 'type'        => 'in',
                                             ]);

                $transfers[] = $transfer;

                $transaction->update([
                                         'status' => 'transfered',
                                     ]);

                $company->update([
                                     'balance' => intval($company->balance) + intval(preg_replace("/[^0-9]/", "", $t['value'])),
                                 ]);

            } catch (\Exception $e) {
                report($e);
                continue;
            }
        }

        Log::info('transferencias criadas ' . print_r($transfers, true));
    }

}
