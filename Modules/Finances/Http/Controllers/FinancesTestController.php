<?php

namespace Modules\Finances\Http\Controllers;

use App\Entities\Sale;
use App\Entities\Transaction;
use App\Entities\Transfer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinancesTestController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesTestController extends Controller
{
    public function index()
    {
        $transactionModel = new Transaction();
        $transferModel    = new Transfer();
        $saleModel        = new Sale();

        /* $transactionsIn = $transferModel->select('transfers.*', 'transaction.sale', 'transaction.company', 'transaction.currency')
                                         ->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction')
                                         ->where('transfers.company_id', 13)->where('transfers.type_enum', 1)
                                         ->orWhere('transaction.company', 13)
                                         ->orderBy('id', 'DESC')->get();*/

        /*$transactionsOut = $transferModel->select('transfers.*', 'transaction.sale', 'transaction.company', 'transaction.currency')
                                         ->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction')
                                         ->where('transfers.company_id', 13)->where('transfers.type', 'in')
                                         ->orWhere('transaction.company', 13)
                                         ->orderBy('id', 'DESC')->get();*/

        $sales = $saleModel->with([
                                      'transactions' => function($query) {
                                          $query->whereNotNull('company');
                                      },
                                  ])->where('status', 1)->whereIn('owner', [14, 21])->get();
        /* $sales = $saleModel->with('clientModel')->find(Hashids::decode('vlDW0ZaLAd3N7Eo'));
         */
        $pendingBalance = 0;

        $pendingTransactions = $transactionModel->where('company', 13)
                                                ->where('status', 'paid')
                                                ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                ->get()->toArray();

        if (count($pendingTransactions)) {
            foreach ($pendingTransactions as $pendingTransaction) {
                $pendingBalance += $pendingTransaction['value'];
            }
        }

        $anticipatedTransactions = $transactionModel->where('company', 13)
                                                    ->where('status', 'anticipated')
                                                    ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                    ->get()->toArray();

        if (count($anticipatedTransactions)) {
            foreach ($anticipatedTransactions as $anticipatedTransaction) {
                $pendingBalance += $anticipatedTransaction['value'] - $anticipatedTransaction['antecipable_value'];
            }
        }

        $cont    = 0;
        $contOut = 0;
        $qt      = 0;
        foreach ($sales as $transaction) {
            /* if ($transaction->value == 991774 ) {
                 dd($transaction->value);
             }*/
            $cont += preg_replace('/\D/', '', $transaction->transactions->first()->value);
            $qt++;
        }
        /*
                foreach ($transactionsOut as $transactionOut) {

                    $contOut += preg_replace('/\D/', '', $transactionOut->value);
                    $qt++;
                }*/

        dd($pendingBalance, $qt, number_format($cont / 100, 2, ',', '.'), number_format($contOut / 100, 2, ',', '.'), number_format(($cont - $contOut) / 100, 2, ',', '.'));
    }
}


