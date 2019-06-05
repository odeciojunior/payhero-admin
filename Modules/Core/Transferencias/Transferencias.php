<?php

namespace Modules\Core\Tranferencias;

use Carbon\Carbon;
use App\Entities\Company;
use App\Entities\Transaction;
use Modules\Core\Sms\SmsService;

class Transferencias {

    public static function verify(){

        $transactions = Transaction::where('delivery_date',Carbon::now()->format('Y-m-d'))->get()->toArray();

        foreach($transactions as $t){

            $company = Company::find($t['company']);

            Transfer::create([
                'transaction' => $t['id'],
                'user'        => $company['user'],
                'value'       => $t['value'],
                'type'        => 'in',
            ]);

            $transacao = Transaction::find($t['id']);

            $transacao->update([
                'status' => 'transfered'
            ]);

            $company->update([
                'balance' => $company['balance'] + substr_replace($t['value'], '.',strlen($t['value']) - 2, 0 ) 
            ]);
        }
    }


}
