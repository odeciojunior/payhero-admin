<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DashboardApiController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {

            $companies = auth()->user()->companies()->get() ?? collect();
            $values    = $this->getDataValues($companies->first()->id_code ?? null);

            return response()->json(compact('companies', 'values'), 200);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da dashboard (DashboardApiController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
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

                $values = $this->getDataValues($request->company);

                if ($values) {
                    return response()->json($values, 200);
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

    /**
     * @param $companyHash
     * @return array
     */
    private function getDataValues($companyHash)
    {
        try {
            if ($companyHash) {
                $companyModel     = new Company();
                $saleModel        = new Sale();
                $transactionModel = new Transaction();
                $companyId        = current(Hashids::decode($companyHash));
                $company          = $companyModel->find($companyId);

                if (!empty($company)) {
                    $pendingBalance = 0;
                    $todayBalance   = 0;

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
                    $userCompanies = $companyModel->where('user_id', auth()->user()->id)
                                                  ->pluck('id')
                                                  ->toArray();
                    $sales         = $saleModel->with([
                                                          'transactions' => function($query) use ($userCompanies) {
                                                              $query->whereIn('company_id', $userCompanies);
                                                          },
                                                      ])
                                               ->whereDate('end_date', Carbon::today()
                                                                             ->toDateString())->get();
                    if (count($sales)) {
                        foreach ($sales as $sale) {
                            foreach ($sale->transactions as $transaction) {
                                $todayBalance += $transaction->value;
                            }
                        }
                    }

                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    return [
                        'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                        'total_balance'     => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                        'pending_balance'   => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                        'today_balance'     => number_format(intval($todayBalance) / 100, 2, ',', '.'),
                        'currency'          => $company->country == 'usa' ? '$' : 'R$',
                    ];
                } else {
                    return [];
                }
            } else {
                return [];
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da empresa (DashboardApiController - getDataValues)');
            report($e);

            return [];
        }
    }
}
