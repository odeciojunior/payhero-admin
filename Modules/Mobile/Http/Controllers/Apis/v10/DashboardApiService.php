<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DashboardApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class DashboardApiService {

    /**
     * DashboardApiService constructor.
     */
    public function __construct() { }

    /**
     * @param $projectId
     * @return JsonResponse
     */
    public function getTopProducts($projectId) {

        $salesModel = new Sale();
        $planModel  = new Plan();

        $itens = $salesModel
            ->select(\DB::raw('count(*) as count'), 'plan_sale.plan_id')
            ->leftJoin('plans_sales as plan_sale', function($join) {
                $join->on('plan_sale.sale_id', '=', 'sales.id');
            })
            ->where('sales.status', 1)->where('sales.owner_id', auth()->user()->id);

        if (!empty($requestStartDate) && !empty($requestEndDate)) {
            $itens->whereBetween('sales.start_date', [$requestStartDate, date('Y-m-d', strtotime($requestEndDate . ' + 1 day'))]);
        } else {
            if (!empty($requestStartDate)) {
                $itens->whereDate('sales.start_date', '>=', $requestStartDate);
            }

            if (!empty($requestEndDate)) {
                $itens->whereDate('sales.end_date', '<', date('Y-m-d', strtotime($requestEndDate . ' + 1 day')));
            }
        }

        $itens = $itens->groupBy('plan_sale.plan_id')->orderBy('count', 'desc')->limit(2)->get()->toArray();
        $plans = [];
        foreach ($itens as $key => $iten) {
            $plan                      = $planModel->with('products')->find($iten['plan_id']);
            $plans[$key]['name']       = $plan->name . ' - ' . $plan->description;
            $plans[$key]['photo']      = $plan->products[0]->photo;
            $plans[$key]['quantidade'] = $iten['count'];
            unset($plan);
        }
        return $plans;
    }

    public function getAccumulated() {

        $salesModel = new Sale();
        $withdrawalModel  = new Withdrawal();

        $dateInit            = new Carbon('first day of this month');
        $dateEnd             = new Carbon('last day of this month');
        $minuteInit          = ' 00:00:00';
        $minuteEnd           = ' 23:59:59';
        $saleSumFloat        = 0;
        $withDrawalsFloat    = 0;
        $accumulated         = [];
        $accumulatedValue    = 0;

        $companies = auth()->user()->companies()->get() ?? collect();

        for ($i = 1; $i <= 3; $i++) {

            $dateInit = $dateInit->subMonths(1);
            $dateEnd = $dateEnd->subMonths(1);

            $salesSum = $salesModel
                ->select(\DB::raw('(CASE
                                        WHEN SUM(total_paid_value) IS NULL
                                        THEN 0
                                        ELSE SUM(total_paid_value)
                                    END) as total_sales'))
                ->where('sales.status', 1)
                ->where('sales.owner_id', auth()->user()->id)
                ->whereBetween('start_date', [$dateInit->format('Y-m-d') . $minuteInit, $dateEnd->format('Y-m-d') . $minuteEnd])
                ->first();

            $withDrawals = $withdrawalModel
                ->select(\DB::raw('(CASE
                                        WHEN SUM(value) IS NULL
                                        THEN 0
                                        ELSE SUM(value)
                                    END) as saque'))
                ->where('status', 3)
                ->whereBetween('created_at', [$dateInit->format('Y-m-d') . $minuteInit, $dateEnd->format('Y-m-d') . $minuteEnd])
                ->whereIn('company_id', $companies->pluck('id'))
                ->first();

            $saleSumStr = str_replace('.', '', $salesSum->total_sales);
            $withDrawalsStr = $withDrawals->saque;
            $withDrawalsStr = number_format($withDrawalsStr,0,'.','');


            if ($saleSumStr != "0") {
                $accumulatedValue = $saleSumStr - $withDrawalsStr;

                if ($accumulatedValue < 0) {
                    $accumulatedValue = 0;
                }
            }

            $accumulated[] = [
                'month'     => strtoupper($dateInit->locale('pt')->getTranslatedShortMonthName('MMM')),
                'value'     => $accumulatedValue
            ];

            $accumulatedValue = 0;
        }


        return $accumulated;
    }


    public function getMetrics() {

        $salesModel = new Sale();
        $withdrawalModel  = new Withdrawal();

        $today               = Carbon::now();
        $yesterday           = Carbon::yesterday();
        $minuteInit          = ' 00:00:00';
        $minuteEnd           = ' 23:59:59';

        $salesSumToday = $salesModel
            ->select(\DB::raw('(CASE
                                    WHEN SUM(total_paid_value) IS NULL
                                    THEN 0
                                    ELSE SUM(total_paid_value)
                                END) as total_sales'))
            ->where('sales.status', 1)
            ->where('sales.owner_id', auth()->user()->id)
            ->whereBetween('start_date', [$today->format('Y-m-d') . $minuteInit, $today->format('Y-m-d') . $minuteEnd])
            ->first();

        $salesSumYesterday = $salesModel
            ->select(\DB::raw('(CASE
                                    WHEN SUM(total_paid_value) IS NULL
                                    THEN 0
                                    ELSE SUM(total_paid_value)
                                END) as total_sales'))
            ->where('sales.status', 1)
            ->where('sales.owner_id', auth()->user()->id)
            ->whereBetween('start_date', [$yesterday->format('Y-m-d') . $minuteInit, $yesterday->format('Y-m-d') . $minuteEnd])
            ->first();

        return $salesSumYesterday->total_sales > 0 ? (($salesSumToday->total_sales / $salesSumYesterday->total_sales) * 100) : $salesSumToday->total_sales > 0 ? 100 : 0;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDashboardValues(Request $request) {

        try {
            $projectId  = current(Hashids::decode($request->input('project')));
            $companies = auth()->user()->companies()->get() ?? collect();

            if ($request->input('company') != "") {
                $companyId  = current(Hashids::decode($request->input('company')));
                $values    = $this->getDataValues($request->input('company') ?? null);
            } else {
                $values    = $this->getDataValues($companies->first()->id_code ?? null);
            }

            $products  = $this->getTopProducts($projectId);
            $metrics   = $this->getMetrics();
            $chart     = $this->getAccumulated();

            return response()->json(compact('companies', 'values', 'products', 'metrics', 'chart'), 200);

        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da dashboard (DashboardApiService - index)');
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
                        ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
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
                    ])->where('status', '1')
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
                    return [
                        'message' => 'Erro => $company não foi encontrada (DashboardApiController - getDataValues)',
                    ];
                }
            } else {
                return [
                    'message' => 'Erro => $company não foi encontrada (DashboardApiController - getDataValues)',
                ];
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da empresa (DashboardApiController - getDataValues)');
            report($e);

            return [
                'message' => 'Erro ao buscar dados da empresa (DashboardApiController - getDataValues)',
            ];
        }
    }

}
