<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserTerms;
use Spatie\Activitylog\Models\Activity;
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
            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou Dashboard');

            $companyModel   = new Company();
            $userTermsModel = new UserTerms();

            $userLogged = auth()->user();

            $userTerm = $userTermsModel->where([['accepted_at', true], ['term_version', 'v1'], ['user_id', $userLogged->id]])
                                       ->first();

            $userTerm = $userTerm ?? false;

            $companies = $companyModel->where('user_id', $userLogged->account_owner_id)->get() ?? collect();

            return response()->json(compact('companies', 'userTerm'), 200);
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
                $userId           = auth()->user()->account_owner_id;

                if (!empty($company)) {
                    //Balance
                    $pendingBalance = $transactionModel->where('company_id', $company->id)
                                                       ->where('status_enum', $transactionModel->present()
                                                                                               ->getStatusEnum('paid'))
                                                       ->whereDate('release_date', '>', Carbon::today()
                                                                                              ->toDateString())
                                                       ->sum('value');

                    $todayBalance = $saleModel
                        ->join('transactions as t', 't.sale_id', '=', 'sales.id')
                        ->where('t.company_id', $companyId)
                        ->where('sales.status', $saleModel->present()->getStatus('approved'))
                        ->whereDate('sales.end_date', Carbon::today()->toDateString())
                        ->sum('t.value');

                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    //Chargeback
                    $chargebackData = $saleModel->selectRaw("SUM(CASE WHEN sales.status = 4 THEN 1 ELSE 0 END) AS contSalesChargeBack,
                                                             SUM(CASE WHEN sales.status = 1 THEN 1 ELSE 0 END) AS contSalesApproved")
                                                ->where('payment_method', 1)
                                                ->where('owner_id', $userId)
                                                ->whereHas('transactions', function($query) use ($companyId) {
                                                    $query->where('company_id', $companyId);
                                                })->first();

                    $totalSalesChargeBack = $chargebackData->contSalesChargeBack;

                    $totalSalesApproved = $chargebackData->contSalesApproved + $chargebackData->contSalesChargeBack;

                    if ($totalSalesChargeBack) {
                        $chargebackTax = ($totalSalesChargeBack * 100) / $totalSalesApproved;
                    } else {
                        $chargebackTax = "0.00";
                    }

                    //News and releases
                    $newsData = settings()->group('dashboard_news')->all(true);
                    $news     = [];
                    foreach ($newsData as $key => $value) {
                        $news[] = json_decode($value, false, 512, JSON_UNESCAPED_UNICODE);
                    }

                    $releasesData = settings()->group('dashboard_releases')->all(true);
                    $releases     = [];
                    foreach ($releasesData as $key => $value) {
                        $releases[$key] = json_decode($value, false, 512, JSON_UNESCAPED_UNICODE);
                    }
                    //Trackings
                    $informedTrackings = DB::table("products_plans_sales as pps")
                                           ->selectRaw("ROUND(100 - IFNULL(SUM(t.id IS NULL) * 100 / COUNT(*), 100), 2) AS total,
                                                ROUND(100 - IFNULL(SUM(s.end_date >= DATE_SUB(CURDATE(), interval 10 day) and t.id is null) * 100 / SUM(s.end_date >= DATE_SUB(CURDATE(), interval 10 day)), 100), 2) AS last_10_days,
                                                ROUND(100 - IFNULL(SUM(s.end_date >= DATE_SUB(CURDATE(), interval 30 day) and t.id is null) * 100 / SUM(s.end_date >= DATE_SUB(CURDATE(), interval 30 day)), 100), 2) AS last_30_days")
                                           ->join('sales as s', 's.id', '=', 'pps.sale_id')
                                           ->leftJoin('trackings as t', 't.product_plan_sale_id', '=', 'pps.id')
                                           ->where('s.owner_id', $userId)
                                           ->where('s.status', $saleModel->present()->getStatus('approved'))
                                           ->first();

                    return [
                        'available_balance'      => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                        'total_balance'          => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                        'pending_balance'        => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                        'today_balance'          => number_format(intval($todayBalance) / 100, 2, ',', '.'),
                        'currency'               => 'R$',
                        'total_sales_approved'   => $totalSalesApproved ?? 0,
                        'total_sales_chargeback' => $totalSalesChargeBack ?? 0,
                        'chargeback_tax'         => $chargebackTax ?? "0.00%",
                        'news'                   => $news,
                        'releases'               => $releases,
                        'trackings'              => $informedTrackings,
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
