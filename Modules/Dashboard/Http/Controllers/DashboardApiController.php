<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\ReportService;
use Modules\Core\Services\UserService;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class DashboardApiController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            activity()->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou Dashboard');

            $companies = (new Company())->where('user_id', auth()->user()->account_owner_id)
                    ->orderBy('order_priority')
                    ->get() ?? collect();

            GetnetBackOfficeService::dispatchGetnetGetDiscountsJob();

            return response()->json(['companies' => $companies]);
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

    public function getValues(Request $request): JsonResponse
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

    private function getDataValues($companyHash): array
    {
        return array (

            'available_balance' => '3.637,98',
            'total_balance' => '161.956,78',
            'pending_balance' => '74.409,27',
            'today_balance' => '0,00',
            'currency' => 'R$',
            'total_sales_approved' => 41050,
            'total_sales_chargeback' => '1211',
            'chargeback_tax' => 2.9500609013398296,
            'trackings' =>
                array (
                    'total' => 55997,
                    'unknown' => '509',
                    'problem' => '195',
                    'average_post_time' => '18',
                    'oldest_sale' => 439,
                    'unknown_percentage' => '0.91',
                    'problem_percentage' => '0.35',
                ),
            'tickets' =>
                array (
                    'total' => 10544,
                    'open' => '372',
                    'closed' => '10128',
                    'mediation' => '44',
                ),
            'blocked_balance' => '83.909,53',
            'blocked_balance_invite' => '0,00',
            'blocked_balance_pending' => '149,47',
            'blocked_balance_total' => '84.059,00',
        );
        try {
            if (empty($companyHash)) {
                return [];
            }
            $companyModel = new Company();
            $saleModel = new Sale();
            $transactionModel = new Transaction();
            $ticketsModel = new Ticket();
            $companyId = current(Hashids::decode($companyHash));
            $company = $companyModel->find($companyId);
            $userId = auth()->user()->account_owner_id;
            $companyService = new CompanyService();

            if (empty($company)) {
                return [];
            }
            $blockedBalancePending = $companyService->getBlockedBalancePending($company);

            $blockedBalance = $companyService->getBlockedBalance($company);
            $pendingBalance = $companyService->getPendingBalance(
                    $company,
                    CompanyService::STATEMENT_AUTOMATIC_LIQUIDATION_TYPE
                ) + $companyService->getPendingBalance($company, CompanyService::STATEMENT_MANUAL_LIQUIDATION_TYPE);

            $availableBalance = $companyService->getAvailableBalance(
                    $company
                ) - $blockedBalance->from_sales - $blockedBalance->from_invites;
            $totalBalance = $availableBalance + $pendingBalance + $blockedBalance->from_sales + $blockedBalance->from_invites;
            $blockedBalanceTotal = $blockedBalance->from_sales + $blockedBalance->from_invites + $blockedBalancePending;

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
                ->first();

            $totalSalesChargeBack = $chargebackData->contSalesChargeBack;

            $totalSalesApproved = $chargebackData->contSalesApproved + $chargebackData->contSalesChargeBack;

            if ($totalSalesChargeBack) {
                $chargebackTax = ($totalSalesChargeBack * 100) / $totalSalesApproved;
            } else {
                $chargebackTax = "0.00";
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
                ->selectRaw(
                    "count(*) as total,
                                               ifnull(sum(if(t.id is null, 1, 0)), 0) as unknown,
                                               ifnull(sum(if(t.system_status_enum in ({$trackingSystemStatus}), 1, 0)), 0) as problem,
                                               ifnull(ceil(avg(timestampdiff(day, s.end_date, t.created_at))), 0) as average_post_time,
                                               ifnull(max(if(t.id is null, timestampdiff(day, s.end_date, now()), 0)), 0) as oldest_sale"
                )
                ->join('sales as s', 's.id', '=', 'pps.sale_id')
                ->leftJoin('trackings as t', 't.product_plan_sale_id', '=', 'pps.id')
                ->leftJoin('products as p', 'p.id', '=', 'pps.product_id')
                ->where('s.owner_id', $userId)
                ->where('s.status', $saleModel->present()->getStatus('approved'))
                ->where('p.type_enum', (new Product)->present()->getType('physical'))
                ->first();

            $trackingsInfo->unknown_percentage = $trackingsInfo->total ? number_format(
                $trackingsInfo->unknown / $trackingsInfo->total * 100,
                2
            ) : '0.00';
            $trackingsInfo->problem_percentage = $trackingsInfo->total ? number_format(
                $trackingsInfo->problem / $trackingsInfo->total * 100,
                2
            ) : '0.00';

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
            )->join('sales', 'tickets.sale_id', '=', 'sales.id')
                ->where('sales.owner_id', $userId)
                ->first();

            return [
                'level_icon' => 'https://img2.gratispng.com/20180404/zse/kisspng-talisman-shendu-fan-art-cartoon-magic-jackie-chan-5ac4b2dd65f083.4597969715228402854176.jpg',
                'level_description' => 'Aventureiro',
                'level' => 'Nível 2' ? 'Pronto para começar?' : 'Nível 2',
                'achievements' => [
                    [
                        'icon' => 'https://img2.gratispng.com/20180516/sre/kisspng-talisman-adventure-film-rooster-animated-series-5afc2c88aedca1.4754026115264759127162.jpg',
                    ],
                    [
                        'icon' => 'https://img2.gratispng.com/20180516/sre/kisspng-talisman-adventure-film-rooster-animated-series-5afc2c88aedca1.4754026115264759127162.jpg',
                    ],
                ],
                'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                'total_balance' => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                'pending_balance' => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                'today_balance' => number_format(intval($todayBalance) / 100, 2, ',', '.'),
                'currency' => 'R$',
                'total_sales_approved' => $totalSalesApproved ?? 0,
                'total_sales_chargeback' => $totalSalesChargeBack ?? 0,
                'chargeback_tax' => $chargebackTax ?? "0.00%",
                'trackings' => $trackingsInfo,
                'tickets' => $tickets,
                'blocked_balance' => number_format(intval($blockedBalance->from_sales) / 100, 2, ',', '.'),
                'blocked_balance_invite' => number_format(intval($blockedBalance->from_invites) / 100, 2, ',', '.'),
                'blocked_balance_pending' => number_format(intval($blockedBalancePending) / 100, 2, ',', '.'),
                'blocked_balance_total' => number_format(intval($blockedBalanceTotal) / 100, 2, ',', '.'),
            ];
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function verifyPendingData(): JsonResponse
    {
        try {
            $user = auth()->user();
            $companyModel = new Company();
            $userService = new UserService();
            $companyService = new CompanyService();
            $companies = $companyModel->where('user_id', $user->account_owner_id)->where('active_flag', true)->orderBy(
                'order_priority'
            )
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

    public function getReleases(): JsonResponse
    {
        $releasesData = settings()->group('dashboard_releases')->all(true);
        $releases = [];
        foreach ($releasesData as $key => $value) {
            $releases[$key] = json_decode($value, false, 512, JSON_UNESCAPED_UNICODE);
        }

        return response()->json(
            [
                'releases' => $releases,
            ]
        );
    }

    public function getChartData(Request $request)
    {
        $data = \request()->all();
        $companyId = current(Hashids::decode($data['company']));

        $reportService =  new ReportService();

        $data = $reportService->getDashboardChartData($companyId);

        return response()->json($data, Response::HTTP_OK);
    }

    public  function getPerformace(Request $request): JsonResponse
    {
        try {
            if ($request->has('company') && !empty($request->input('company'))) {
                $values = $this->getDataPerformace($request->company);

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

    private function getDataPerformace($companyHash): array
    {
        return array (
            'level' => 1,
            'achievements' =>
                array (
                    0,
                    1,
                    2,
                    3
                ),
            'tasks' =>
                array (
                    0 =>
                        array (
                            'task' => 'Tenha seus documentos aprovados',
                            'status' => 1,
                        ),
                    1 =>
                        array (
                            'task' => 'Cadastre sua primeira loja',
                            'status' => 0,
                        ),
                    2 =>
                        array (
                            'task' => 'Faça sua primeira venda',
                            'status' => 0,
                        ),
                    3 =>
                        array (
                            'task' => 'Fature R$1.000 (R$0 / R$1000)',
                            'status' => 0,
                        ),
                    4 =>
                        array (
                            'task' => 'Faça sua primeira venda',
                            'status' => 0,
                        ),
                    5 =>
                        array (
                            'task' => 'Faça sua primeira venda',
                            'status' => 0,
                        ),
                    6 =>
                        array (
                            'task' => 'Faça sua primeira venda',
                            'status' => 0,
                        ),
                    7 =>
                        array (
                            'task' => 'Faça sua primeira venda',
                            'status' => 0,
                        ),
                    8 =>
                        array (
                            'task' => 'Faça sua primeira venda',
                            'status' => 0,
                        ),
                ),
            'progress' => '80',
            'benefits' =>
                array (
                    0 =>
                        array (
                            'card' => 'NÍVEL 2',
                            'benefit' => 'Cashback de 0,5%',
                            'status' => 1,
                        ),
                    1 =>
                        array (
                            'card' => 'NÍVEL 3',
                            'benefit' => 'Recebimentos mais rápidos',
                            'status' => 0,
                        ),
//                    2 =>
//                        array (
//                            'card' => 'NÍVEL 2',
//                            'benefit' => 'Gerente de Contas',
//                            'status' => 0,
//                        ),
//                    3 =>
//                        array (
//                            'card' => 'NÍVEL 2',
//                            'benefit' => 'Gerente de Contas',
//                            'status' => 0,
//                        ),
                ),
            'money_cashback' => '95,66',
        );
    }

    public  function getAccountHealth(Request $request): JsonResponse
    {
        try {
            if ($request->has('company') && !empty($request->input('company'))) {
                $values = $this->getDataAccountHealth($request->company);

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

    private function getDataAccountHealth($companyHash): array
    {
        return array (
            'level' => 1,
            'score' => '1.5',
            'chargeback_score' => '7.9',
            'attendance_score' => '8',
            'tracking_score' => '10',
        );
    }

    public  function getAccountChargeback(Request $request): JsonResponse
    {
        try {
            if ($request->has('company') && !empty($request->input('company'))) {
                $values = $this->getDataAccountChargeback($request->company);

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

    private function getDataAccountChargeback($companyHash): array
    {
        return array (
            'chargeback_score' => '7.9',
            'chargeback_tax' => '2.962099691650278',
            'total_sales_approved' => '1.522', //'41187',
            'total_sales_chargeback' => '1220',
        );
    }

    public  function getAccountAttendance(Request $request): JsonResponse
    {
        try {
            if ($request->has('company') && !empty($request->input('company'))) {
                $values = $this->getDataAccountAttendance($request->company);

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

    private function getDataAccountAttendance($companyHash): array
    {
        return array (
            'attendance_score' => '8.1',
            'total' => '10688',
            'open' => '250',
            'closed' => '10411',
            'mediation' => '27',
        );
    }

    public  function getAccountTracking(Request $request): JsonResponse
    {
        try {
            if ($request->has('company') && !empty($request->input('company'))) {
                $values = $this->getDataAccountTracking($request->company);

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

    private function getDataAccountTracking($companyHash): array
    {
        return array (
            'tracking_score' => '10',
            'average_post_time' => '18',
            'oldest_sale' => '448',
            'problem' => '195',
            'problem_percentage' => '0.35',
            'unknown' => '618',
            'unknown_percentage' => '1.10',
        );
    }
}
