<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserTerms;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DashboardApiController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            activity()->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou Dashboard');

            $companyModel = new Company();
            $userTermsModel = new UserTerms();

            $userLogged = auth()->user();

            $userTerm = true;
            if (!$request->has('skip')) {
                $userTerm = $userTermsModel->whereNotNull('accepted_at')
                    ->where([['term_version', 'v1'], ['user_id', $userLogged->id]])
                    ->exists();
            }

            $companies = $companyModel->where('user_id', $userLogged->account_owner_id)->orderBy('order_priority')
                    ->get() ?? collect();

            return response()->json(compact('companies', 'userTerm'), 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
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
                    return response()->json(
                        [
                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
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
                $ticketsModel = new Ticket();
                $companyId = current(Hashids::decode($companyHash));
                $company = $companyModel->find($companyId);
                $userId = auth()->user()->account_owner_id;
                $companyService = new CompanyService();

                if (!empty($company)) {
                    //Balance
                    $pendingBalance = $companyService->getPendingBalance($company);

                    $statusArray = [
                        $transactionModel->present()->getStatusEnum('paid'),
                        $transactionModel->present()->getStatusEnum('transfered'),
                    ];

                    $todayBalance = $saleModel
                        ->join('transactions as t', 't.sale_id', '=', 'sales.id')
                        ->where('t.company_id', $companyId)
                        ->whereDate('sales.end_date', Carbon::today()->toDateString())
                        ->whereIn('t.status_enum', $statusArray)
                        ->sum('t.value');

                    $availableBalance = $company->balance;
                    $totalBalance = $availableBalance + $pendingBalance;

                    //Chargeback
                    $chargebackData = $saleModel->selectRaw(
                        "SUM(CASE WHEN sales.status = 4 THEN 1 ELSE 0 END) AS contSalesChargeBack,
                                                             SUM(CASE WHEN sales.status = 1 THEN 1 ELSE 0 END) AS contSalesApproved"
                    )
                        ->where('payment_method', 1)
                        ->where('owner_id', $userId)
                        ->whereHas(
                            'transactions',
                            function ($query) use ($companyId) {
                                $query->where('company_id', $companyId);
                            }
                        )
//                        ->where(
//                            function ($q1) {
//                                $q1->where('status', 4)
//                                    ->whereDoesntHave(
//                                        'saleLogs',
//                                        function ($querySaleLog) {
//                                            $querySaleLog->whereIn('status_enum', collect([20, 7]));
//                                        }
//                                    )->orWhere('status', 1);
//                            }
//                        )
                        ->first();

                    $totalSalesChargeBack = $chargebackData->contSalesChargeBack;

                    $totalSalesApproved = $chargebackData->contSalesApproved + $chargebackData->contSalesChargeBack;

                    if ($totalSalesChargeBack) {
                        $chargebackTax = ($totalSalesChargeBack * 100) / $totalSalesApproved;
                    } else {
                        $chargebackTax = "0.00";
                    }

                    //News and releases
                    $newsData = settings()->group('dashboard_news')->all(true);
                    $news = [];
                    foreach ($newsData as $key => $value) {
                        $newsDecoded = json_decode($value, false, 512, JSON_UNESCAPED_UNICODE);
                        if (strpos($newsDecoded->title, '{nome_usuario}') !== false) {
                            $userFirstName = explode(' ', auth()->user()->name)[0];
                            $newsDecoded->title = str_replace(
                                '{nome_usuario}',
                                ucfirst($userFirstName),
                                $newsDecoded->title
                            );
                        }
                        $news[] = $newsDecoded;
                    }

                    $releasesData = settings()->group('dashboard_releases')->all(true);
                    $releases = [];
                    foreach ($releasesData as $key => $value) {
                        $releases[$key] = json_decode($value, false, 512, JSON_UNESCAPED_UNICODE);
                    }
                    //Trackings
                    $trackingPresenter = (new Tracking())->present();
                    $trackingSystemStatus = [
                        $trackingPresenter->getSystemStatusEnum('no_tracking_info'),
                        $trackingPresenter->getSystemStatusEnum('unknown_carrier'),
                        $trackingPresenter->getSystemStatusEnum('posted_before_sale'),
                        $trackingPresenter->getSystemStatusEnum('duplicated'),
                    ];
                    $trackingSystemStatus = implode(',', $trackingSystemStatus);

                    $trackingsInfo = DB::table("products_plans_sales as pps")
                        ->selectRaw("count(*) as total,
                                               ifnull(sum(if(t.id is null, 1, 0)), 0) as unknown,
                                               ifnull(sum(if(t.system_status_enum in ({$trackingSystemStatus}), 1, 0)), 0) as problem,
                                               ifnull(ceil(avg(timestampdiff(day, s.end_date, t.created_at))), 0) as average_post_time,
                                               ifnull(max(if(t.id is null, timestampdiff(day, s.end_date, now()), 0)), 0) as oldest_sale")
                        ->join('sales as s', 's.id', '=', 'pps.sale_id')
                        ->leftJoin('trackings as t', 't.product_plan_sale_id', '=', 'pps.id')
                        ->where('s.owner_id', $userId)
                        ->where('s.status', $saleModel->present()->getStatus('approved'))
                        ->first();

                    $trackingsInfo->unknown_percentage = $trackingsInfo->total ? number_format($trackingsInfo->unknown / $trackingsInfo->total * 100, 2) : '0.00';
                    $trackingsInfo->problem_percentage = $trackingsInfo->total ? number_format($trackingsInfo->problem / $trackingsInfo->total * 100, 2) : '0.00';

                    //Tickets
                    $statusArray = [
                        $ticketsModel->present()->getTicketStatusEnum('open'),
                        $ticketsModel->present()->getTicketStatusEnum('closed'),
                        $ticketsModel->present()->getTicketStatusEnum('mediation'),
                    ];

                    $tickets = $ticketsModel->selectRaw(
                        "count(*) as total,
                                                         sum(case when ticket_status_enum = ? then 1 else 0 end) as open,
                                                         sum(case when ticket_status_enum = ? then 1 else 0 end) as closed,
                                                         sum(case when ticket_status_enum = ? then 1 else 0 end) as mediation",
                        $statusArray
                    )
                        ->join('sales', 'tickets.sale_id', '=', 'sales.id')
                        ->where('sales.owner_id', $userId)
                        ->first();

                    return [
                        'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                        'total_balance' => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                        'pending_balance' => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                        'today_balance' => number_format(intval($todayBalance) / 100, 2, ',', '.'),
                        'currency' => 'R$',
                        'total_sales_approved' => $totalSalesApproved ?? 0,
                        'total_sales_chargeback' => $totalSalesChargeBack ?? 0,
                        'chargeback_tax'         => $chargebackTax ?? "0.00%",
                        'news'                   => $news,
                        'releases'               => $releases,
                        'trackings'              => $trackingsInfo,
                        'tickets'                => $tickets,
                    ];
                } else {
                    return [];
                }
            } else {
                return [];
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function verifyPendingData()
    {
        try {
            $user = auth()->user();
            $companyModel = new Company();
            $userService = new UserService();
            $companyService = new CompanyService();
            $companies = $companyModel->where('user_id', $user->account_owner_id)->orderBy('order_priority')
                ->get();


            if ($user->created_at <= '2020-07-01') {
                $pendingUserData = $userService->verifyFieldsEmpty($user);
            } else {
                $pendingUserData = false;
            }

            $companyArray = [];
            foreach ($companies as $company) {
                if ($company->created_at <= '2020-07-01' && $companyService->verifyFieldsEmpty($company)) {
                    $companyArray[] = [
                        'id_code' => Hashids::encode($company->id),
                        'fantasy_name' => $company->company_type == 1 ? 'Pessoa física' : Str::limit(
                                $company->fantasy_name,
                                20
                            ) ?? '',
                        'type' => $company->company_type,
                    ];
                }
            }

            return response()->json(
                [
                    'companies' => $companyArray,
                    'pending_user_data' => $pendingUserData,
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
    }
}
