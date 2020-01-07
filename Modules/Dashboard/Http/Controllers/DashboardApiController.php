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
            $companyModel = new Company();

            $companies = $companyModel->where('user_id', auth()->user()->account_owner_id)->get() ?? collect();

            $values = $this->getDataValues($companies->first()->id_code ?? null);

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
                $companyModel = new Company();
                $saleModel = new Sale();
                $transactionModel = new Transaction();
                $companyId = current(Hashids::decode($companyHash));
                $company = $companyModel->find($companyId);

                if (!empty($company)) {
                    $pendingBalance = 0;
                    $todayBalance = 0;

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
                    $sales = $saleModel->with([
                        'transactions' => function ($query) use ($companyId) {
                            $query->where('company_id', $companyId);
                        },
                    ])->where('status', 1)
                        ->whereDate('end_date', Carbon::today()->toDateString())
                        ->get();

                    $chargebackData = $saleModel->selectRaw("SUM(CASE WHEN sales.status = 4 THEN 1 ELSE 0 END) AS contSalesChargeBack,
                                                             SUM(CASE WHEN sales.status = 1 THEN 1 ELSE 0 END) AS contSalesApproved")
                        ->where('payment_method', 1)
                        ->where('owner_id', 14)
                        ->whereHas('transactions', function ($query) use ($companyId) {
                            $query->where('company_id', $companyId);
                        })->first();

                    $totalSalesChargeBack = $chargebackData->contSalesChargeBack;

                    $totalSalesApproved = $chargebackData->contSalesApproved + $chargebackData->contSalesChargeBack;

                    if ($totalSalesChargeBack) {
                        $chargebackTax = ($totalSalesChargeBack * 100) /  $totalSalesApproved;
                    } else {
                        $chargebackTax = "0.00";
                    }

                    foreach ($sales as $sale) {
                        foreach ($sale->transactions as $transaction) {
                            if ($sale->status == 1) {
                                $todayBalance += $transaction->value;
                            } else {
                                continue;
                            }
                        }
                    }

                    $availableBalance = $company->balance;
                    $totalBalance = $availableBalance + $pendingBalance;

                    return [
                        'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                        'total_balance' => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                        'pending_balance' => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                        'today_balance' => number_format(intval($todayBalance) / 100, 2, ',', '.'),
                        'currency' => $company->country == 'usa' ? '$' : 'R$',
                        'total_sales_approved'   => $totalSalesApproved ?? 0,
                        'total_sales_chargeback' => $totalSalesChargeBack ?? 0,
                        'chargeback_tax'         => $chargebackTax,
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
