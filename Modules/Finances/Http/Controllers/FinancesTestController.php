<?php

namespace Modules\Finances\Http\Controllers;

use App\Entities\Transaction;
use App\Entities\Transfer;
use App\Http\Controllers\Controller;
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

        $transactionsIn = $transferModel->select('transfers.*', 'transaction.sale', 'transaction.company', 'transaction.currency')
                                        ->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction')
                                        ->where('transfers.company_id', 13)
                                        ->orWhere('transaction.company', 13)
                                        ->orderBy('id', 'DESC')->get();

        $cont = 0;
        $qt   = 0;
        foreach ($transactionsIn as $transaction) {
            $cont += preg_replace('/\D/', '', $transaction->value);
            $qt++;
        }

        dd($qt, number_format($cont / 100, 2, ',', '.'));
    }
}


