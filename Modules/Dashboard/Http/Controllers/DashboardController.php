<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DashboardController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        try {
            return view('dashboard::dashboard', [
                'companies' => auth()->user()->companies()->get(),
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar empresas do usuario (Dashboard - index)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getValues(Request $request)
    {
        try {
            if ($request->has('company') && !empty($request->input('company'))) {
                $companyModel     = new Company();
                $transactionModel = new Transaction();
                $companyId        = current(Hashids::decode($request->company));
                $company          = $companyModel->find($companyId);

                if (!empty($company)) {
                    $antecipableBalance = 0;
                    $pendingBalance     = 0;

                    $pendingTransactions = $transactionModel->where('company_id', $company->id)
                                                            ->where('status', 'paid')
                                                            ->whereDate('release_date', '>', Carbon::today()
                                                                                                   ->toDateString())
                                                            ->get();

                    if (count($pendingTransactions)) {
                        foreach ($pendingTransactions as $pendingTransaction) {
                            $pendingBalance += $pendingTransaction->value;
                        }
                    }

                    $anticipatedTransactions = $transactionModel->where('company_id', $company->id)
                                                                ->where('status', 'anticipated')
                                                                ->whereDate('release_date', '>', Carbon::today()
                                                                                                       ->toDateString())
                                                                ->get();

                    if (count($anticipatedTransactions)) {
                        foreach ($anticipatedTransactions as $anticipatedTransaction) {
                            $pendingBalance += $anticipatedTransaction->value - $anticipatedTransaction->antecipable_value;
                        }
                    }

                    $antecipableTransactions = $transactionModel->where('company_id', $company->id)
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
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da dashboard (DashboardController - getValues)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }
}

