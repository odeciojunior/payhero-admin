<?php

namespace Modules\Dashboard\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Dashboard\Transformers\DashboardResumeCollection;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DashboardApiController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardApiController extends Controller
{
    /**
     * @param Request $request
     * @return DashboardResumeCollection
     */
    public function resume(Request $request)
    {
        try {
            //Models Declaration
            $companyModel = new Company();
            //Get Request Parameters
            $company = $request->get('company');
            if (!empty($company)) {
                $company = current(Hashids::decode($company));
            }
            //Main Code
            $companies = $companyModel->newQuery()
                                      ->where('user_id', auth()->id())
                                      ->when(!empty($company), function(Builder $query) use ($company) {
                                          return $query->where('id', $company);
                                      })->get();
            if (empty($companies)) {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                    ], Response::HTTP_BAD_REQUEST
                );
            }
            $companyFilter = implode($companies->pluck('id')->toArray(), ",");
            $sql           = "
                SELECT b.company_id
                , b.fantasy_name
                , b.country
                , (b.today_transaction) AS today_balance
                , (b.pending_transaction + b.anticipated_transaction) AS pending_balance
                , (b.antecipable_transaction) AS antecipable_balance
                , (b.balance) AS available_balance
                , (b.balance + b.pending_transaction + b.anticipated_transaction) AS total_balance
                FROM (
                    SELECT c.id company_id
                    , c.fantasy_name
                    , c.country
                    , COALESCE(c.balance, 0) balance
                    , SUM(CASE WHEN t.status = 'paid' AND t.release_date > CURRENT_DATE THEN COALESCE(t.value, 0) ELSE 0 END) pending_transaction
                    , SUM(CASE WHEN t.status = 'anticipated' AND t.release_date > CURRENT_DATE THEN COALESCE(t.value, 0) - COALESCE(t.antecipable_value, 0) ELSE 0 END) anticipated_transaction
                    , SUM(CASE WHEN t.status = 'paid' AND t.release_date > CURRENT_DATE AND t.antecipation_date <= CURRENT_DATE THEN COALESCE(t.antecipable_value, 0) ELSE 0 END) antecipable_transaction
                    , SUM(CASE WHEN t.status IN ('paid') AND DATE(t.release_date) = CURRENT_DATE THEN COALESCE(t.value, 0) ELSE 0 END) today_transaction
                    FROM companies c
                    LEFT JOIN transactions t ON t.company_id = c.id
                    WHERE 1 = 1
                    AND c.user_id = '" . auth()->id() . "'
                    AND c.id IN (" . $companyFilter . ")
                    GROUP BY c.id
                    , c.fantasy_name 
                    , c.country
                ) b;
            ";
            $result        = collect(DB::select($sql));

            return new DashboardResumeCollection($result);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da dashboard (DashboardApiController - index)');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ], 400
            );
        }
    }

    /**
     * @return JsonResponse
     */
    //    public function index()
    //    {
    //        try {
    //            /** @var User $user */
    //            $user      = auth()->user();
    //            $companies = $user->companies()->get() ?? collect();
    //            $values    = $this->getDataValues($companies->first()->id_code ?? null);
    //
    //            return response()->json(compact('companies', 'values'), 200);
    //        } catch (Exception $e) {
    //            Log::warning('Erro ao buscar dados da dashboard (DashboardApiController - index)');
    //            report($e);
    //
    //            return response()->json(
    //                [
    //                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
    //                ], 400
    //            );
    //        }
    //    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    //    public function getValues(Request $request)
    //    {
    //        try {
    //            if ($request->has('company') && !empty($request->input('company'))) {
    //                $values = $this->getDataValues($request->company);
    //                if ($values) {
    //                    return response()->json($values, 200);
    //                } else {
    //                    return response()->json(
    //                        [
    //                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
    //                        ], 400
    //                    );
    //                }
    //            } else {
    //                return response()->json(
    //                    [
    //                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
    //                    ], 400
    //                );
    //            }
    //        } catch (Exception $e) {
    //            Log::warning('Erro ao buscar dados da dashboard (DashboardController - getValues)');
    //            report($e);
    //
    //            return response()->json(
    //                [
    //                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
    //                ], 400
    //            );
    //        }
    //    }

    /**
     * @param $companyHash
     * @return array
     */
    //    private function getDataValues($companyHash)
    //    {
    //        try {
    //            if ($companyHash) {
    //                $companyModel     = new Company();
    //                $transactionModel = new Transaction();
    //                $companyId        = current(Hashids::decode($companyHash));
    //                $company          = $companyModel->newQuery()->find($companyId);
    //                if (!empty($company)) {
    //                    $antecipableBalance  = 0;
    //                    $pendingBalance      = 0;
    //                    $pendingTransactions = $transactionModel->newQuery()
    //                                                            ->where('company_id', $company->id)
    //                                                            ->where('status', 'paid')
    //                                                            ->whereDate('release_date', '>', now()->startOfDay())
    //                                                            ->get();
    //                    if (count($pendingTransactions)) {
    //                        foreach ($pendingTransactions as $pendingTransaction) {
    //                            $pendingBalance += $pendingTransaction->value;
    //                        }
    //                    }
    //                    $anticipatedTransactions = $transactionModel->newQuery()
    //                                                                ->where('company_id', $company->id)
    //                                                                ->where('status', 'anticipated')
    //                                                                ->whereDate('release_date', '>', now()->startOfDay())
    //                                                                ->get();
    //                    if (count($anticipatedTransactions)) {
    //                        foreach ($anticipatedTransactions as $anticipatedTransaction) {
    //                            $pendingBalance += $anticipatedTransaction->value - $anticipatedTransaction->antecipable_value;
    //                        }
    //                    }
    //                    $antecipableTransactions = $transactionModel->newQuery()
    //                                                                ->where('company_id', $company->id)
    //                                                                ->where('status', 'paid')
    //                                                                ->whereDate('release_date', '>', now()->startOfDay())
    //                                                                ->whereDate('antecipation_date', '<=', now()->startOfDay())
    //                                                                ->get();
    //                    if (count($antecipableTransactions)) {
    //                        foreach ($antecipableTransactions as $antecipableTransaction) {
    //                            $antecipableBalance += $antecipableTransaction->antecipable_value;
    //                        }
    //                    }
    //                    $availableBalance = $company->balance;
    //                    $totalBalance     = $availableBalance + $pendingBalance;
    //
    //                    return [
    //                        'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
    //                        'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
    //                        'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
    //                        'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
    //                        'currency'            => $company->country == 'usa' ? '$' : 'R$',
    //                    ];
    //                } else {
    //                    return [];
    //                }
    //            } else {
    //                return [];
    //            }
    //        } catch (Exception $e) {
    //            Log::warning('Erro ao buscar dados da empresa (DashboardApiController - getDataValues)');
    //            report($e);
    //
    //            return [];
    //        }
    //    }
}
