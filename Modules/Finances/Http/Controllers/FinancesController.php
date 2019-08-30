<?php

namespace Modules\Finances\Http\Controllers;

use App\Entities\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use PagarMe\Client;
use App\Entities\Company;
use Illuminate\Http\Request;
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinancesController
 * @package Modules\Finances\Http\Controllers
 */
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalances()
    {
        $companyModel     = new Company();
        $transactionModel = new Transaction();

        $antecipableBalance = 0;
        $pendingBalance     = 0;

        $company = $companyModel->where('user_id', auth()->user()->id)->first();

        if (!empty($company)) {
            $pendingTransactions = $transactionModel->where('company', $company->id)
                                                    ->where('status', 'paid')
                                                    ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                    ->get()->toArray();

            if (count($pendingTransactions)) {
                foreach ($pendingTransactions as $pendingTransaction) {
                    $pendingBalance += $pendingTransaction['value'];
                }
            }

            $anticipableTransactions = $transactionModel->where('company', $company->id)
                                                        ->where('status', 'anticipated')
                                                        ->whereDate('release_date', '>', Carbon::today()
                                                                                               ->toDateString())
                                                        ->get()->toArray();

            if (count($anticipableTransactions)) {
                foreach ($anticipableTransactions as $anticipableTransaction) {
                    $pendingBalance += $anticipableTransaction['value'] - $anticipableTransaction['antecipable_value'];
                }
            }

            $antecipableTransactions = $transactionModel->where('company', $company->id)->where('status', 'paid')
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
        } else {
            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
        }
    }
}


