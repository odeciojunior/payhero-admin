<?php

namespace Modules\Finances\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinancesApiController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalances(Request $request)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            /** @var Transaction $transactionModel */
            $transactionModel   = new Transaction();
            $antecipableBalance = 0;
            $pendingBalance     = 0;
            if ($request->has('company') && !empty($request->input('company'))) {
                $companyId = current(Hashids::decode($request->input('company')));
                /** @var Company $company */
                $company = $companyModel->newQuery()->find($companyId);
                if (!empty($company)) {
                    //                    $pendingTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                    //                                                            ->where('status', 'paid')
                    //                                                            ->whereDate('release_date', '>', now()->startOfDay())
                    //                                                            ->get();
                    $pendingTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                                                            ->where('status', 'paid')
                                                            ->whereDate('release_date', '>', now()->startOfDay())
                                                            ->select(DB::raw('sum( value ) as pending_balance'))
                                                            ->first();
                    $pendingBalance      += $pendingTransactions->pending_balance;
                    $anticipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                                                                ->where('status', 'anticipated')
                                                                ->whereDate('release_date', '>', now()->startOfDay())
                                                                ->select(DB::raw('sum( value - antecipable_value ) as pending_balance'))
                                                                ->first();
                    $pendingBalance += $anticipableTransactions->pending_balance;
                    $antecipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                                                                ->where('status', 'paid')
                                                                ->whereDate('release_date', '>', Carbon::today())
                                                                ->whereDate('antecipation_date', '<=', Carbon::today())
                                                                ->select(DB::raw('sum( antecipable_value ) as antecipable_balance'))
                                                                ->first();

                    $antecipableBalance += $antecipableTransactions->antecipable_balance;
                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    return response()->json(
                        [
                            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                            'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                            'currency'            => $company->country == 'usa' ? '$' : 'R$',
                        ]
                    );
                } else {
                    return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar da empresa (FinancesController - getBalances)');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu algum erro, tente novamente!',
                ], 400);
        }
    }
}
