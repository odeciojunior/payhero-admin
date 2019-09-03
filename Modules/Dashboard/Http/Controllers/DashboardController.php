<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Transaction;

/**
 * Class DashboardController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard::dashboard', [
            'companies' => auth()->user()->companies()->get()->toArray(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getValues(Request $request)
    {
        $companyModel     = new Company();
        $transactionModel = new Transaction();

        $requestData = $request->all();

        $antecipableBalance = 0;
        $pendingBalance     = 0;

        $company = $companyModel->find($request->company);

        $pendingTransactions = $transactionModel->where('company_id', $request->company)
                                                ->where('status', 'paid')
                                                ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                ->get();

        if (count($pendingTransactions)) {
            foreach ($pendingTransactions as $pendingTransaction) {
                $pendingBalance += $pendingTransaction->value;
            }
        }

        $anticipatedTransactions = $transactionModel->where('company_id', $request->company)
                                                    ->where('status', 'anticipated')
                                                    ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                    ->get();

        if (count($anticipatedTransactions)) {
            foreach ($anticipatedTransactions as $anticipatedTransaction) {
                $pendingBalance += $anticipatedTransaction->value - $anticipatedTransaction->antecipable_value;
            }
        }

        $antecipableTransactions = $transactionModel->where('company_id', $request->company)
                                                    ->where('status', 'paid')
                                                    ->whereDate('release_date', '>', Carbon::today())
                                                    ->whereDate('antecipation_date', '<=', Carbon::today())
                                                    ->get();

        if (count($antecipableTransactions)) {
            foreach ($antecipableTransactions as $antecipableTransaction) {
                $antecipableBalance += $antecipableTransaction->antecipable_value;
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

