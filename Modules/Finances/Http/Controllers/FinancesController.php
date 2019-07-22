<?php

namespace Modules\Finances\Http\Controllers;

use Carbon\Carbon;
use PagarMe\Client;
use App\Entities\Company;
use Illuminate\Http\Request;
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class FinancesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companyModel = new Company();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)->get()->toArray();

        return view('finances::index', [
            'companies' => $userCompanies,
        ]);
    }

    /**
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalances($company_id)
    {
        $companyModel     = new Company();
        $transactionModel = new Transaction();

        $antecipableBalance = 0;
        $pendingBalance     = 0;

        $company = $companyModel->find(current(Hashids::decode($company_id)));

        $pendingTransactions = $transactionModel->where('company', $company->id)
                                                ->where('status', 'paid')
                                                ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                ->get()->toArray();

        if (count($pendingTransactions)) {
            foreach ($pendingTransactions as $pendingTransaction) {
                $pendingBalance += $pendingTransaction['value'];
            }
        }

        $antecipableTransactions = $transactionModel->where('company', $company->id)
                                                    ->whereDate('release_date', '>', Carbon::today())
                                                    ->whereDate('antecipation_date', '<=', Carbon::today())
                                                    ->get()->toArray();

        if (count($antecipableTransactions)) {
            foreach ($antecipableTransactions as $antecipableTransaction) {
                $antecipableBalance += $antecipableTransaction['antecipable_value'];
            }
        }

        $availableBalance = $company->balance;
        $totalBalance     = $availableBalance + $pendingBalance;

        return response()->json([
                                    'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                                    'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                                    'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                                    'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                                    'currency'            => $company->country == 'usa' ? '$' : 'R$',
                                ]);
    }
}


