<?php

namespace Modules\Core\Tranferencias;

use Carbon\Carbon;
use App\Entities\Company;
use App\Entities\Transfer;
use App\Entities\Transaction;
use Modules\Core\Sms\SmsService;
use Illuminate\Support\Facades\Log;

class Transferencias {

    public static function verify(){

        $transactions = Transaction::where([
            ['release_date','<=',Carbon::now()->format('Y-m-d')],
            ['status', 'paid']
        ])->get()->toArray();

        $transfers = [];

        foreach($transactions as $t){

            $company = Company::find($t['company']);

            $transfer = Transfer::create([
                'transaction' => $t['id'],
                'user'        => $company['user_id'],
                'value'       => $t['value'],
                'type'        => 'in',
            ]);

            $transfers[] = $transfer;

            $transaction = Transaction::find($t['id']);

            $transaction->update([
                'status' => 'transfered'
            ]);

            $company->update([
                'balance' => intval($company->balance) +  intval(preg_replace("/[^0-9]/", "", $t['value']))
            ]);
        }

        Log::info('transferencias criadas ' . print_r($transfers, true));
    }

}
