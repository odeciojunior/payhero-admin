<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Console\Commands\UpdateUserAchievements;
use App\Console\Commands\UpdateUserLevel;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\DashboardNotification;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserTerms;
use Modules\Core\Services\AchievementService;
use Modules\Core\Services\BenefitsService;
use Modules\Core\Services\ChargebackService;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\IpService;
use Modules\Core\Services\Reports\ReportService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\TaskService;
use Modules\Core\Services\TrackingService;
use Modules\Core\Services\UserService;
use Modules\Dashboard\Transformers\DashboardAchievementsResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class DashboardApiController extends Controller
{
    public function index()
    {
        try {
            $companies =
                Company::where(
                    "user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId()
                )
                    ->where("active_flag", true)
                    ->orderBy("order_priority")
                    ->get() ?? collect();

            return response()->json(["companies" => $companies]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function getValues(Request $request)
    {
        try {
            $companyId = !empty($request->company_id)
                ? hashids_decode($request->company_id)
                : auth()->user()->company_default;
            $company = Company::find($companyId);

            if (empty($company)) {
                return response()->json(["message" => __('messages.unexpected_error')], 400);
            }

            $todayBalance = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $companyId)
                ->whereDate("sales.end_date", Carbon::today()->toDateString())
                ->whereIn("t.status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->whereNull("t.deleted_at")
                ->sum("t.value");

            $companyService = new CompanyBalanceService($company);
            $balancesResume = $companyService->getResumes();

            $availableBalance = array_sum(array_column($balancesResume, "available_balance"));
            $pendingBalance = array_sum(array_column($balancesResume, "pending_balance"));
            $blockedBalance = array_sum(array_column($balancesResume, "blocked_balance"));
            $securityReserveBalance = array_sum(array_column($balancesResume, "security_reserve_balance"));
            $totalBalance = array_sum(array_column($balancesResume, "total_balance"));

            return response()->json([
                "available_balance" => number_format($availableBalance / 100, 2, ",", "."),
                "pending_balance" => number_format($pendingBalance / 100, 2, ",", "."),
                "security_reserve_balance" => number_format(
                    ($blockedBalance + $securityReserveBalance) / 100,
                    2,
                    ",",
                    "."
                ),
                "total_balance" => number_format($totalBalance / 100, 2, ",", "."),
                "today_balance" => number_format($todayBalance / 100, 2, ",", "."),
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function verifyPendingData()
    {
        try {
            $user = auth()->user();
            $userService = new UserService();
            $companyService = new CompanyService();
            $companies = Company::where("user_id", $user->account_owner_id)
                ->where("active_flag", true)
                ->orderBy("order_priority")
                ->get();

            if ($user->created_at <= "2020-07-01") {
                $pendingUserData = $userService->verifyFieldsEmpty($user);
            } else {
                $pendingUserData = false;
            }

            $companyArray = [];
            foreach ($companies as $company) {
                if ($company->created_at <= "2020-07-01" && $companyService->verifyFieldsEmpty($company)) {
                    $companyArray[] = [
                        "id_code" => Hashids::encode($company->id),
                        "fantasy_name" =>
                            $company->company_type == 1
                                ? "Pessoa física"
                                : Str::limit($company->fantasy_name, 20) ?? "",
                        "type" => $company->company_type,
                    ];
                }
            }

            return response()->json(
                [
                    "companies" => $companyArray,
                    "pending_user_data" => $pendingUserData,
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    public function getChartData(Request $request)
    {
        try {
            $data = \request()->all();
            $companyId = current(Hashids::decode($data["company"]));

            $reportService = new ReportService();

            $data = $reportService->getDashboardChartData($companyId);

            return response()->json($data, Response::HTTP_OK);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    public function getPerformance(Request $request)
    {
        try {
            if (!$request->has("company")) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde",
                    ],
                    400
                );
            }

            $values = $this->getDataPerformance($request->company);

            return response()->json($values, 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    private function getDataPerformance($companyHash)
    {
        try {
            $company = Company::find(current(Hashids::decode($companyHash)));

            if (empty($company)) {
                return response()->json(["message" => "Empresa não encontrada."], 404);
            }

            $user = $company->user;
            $taskService = new TaskService();
            $benefitService = new BenefitsService();
            $achievementService = new AchievementService();

            return [
                "level" => $user->level,
                "achievements" => $achievementService->getCurrentUserAchievements($user),
                "tasks" => $user->level === 1 ? $taskService->getCurrentUserTasks($user) : [],
                "billed" => $user->total_commission_value,
                "money_cashback" => $this->getCashbackReceivedValue(),
                "benefits" => $benefitService->getUserBenefits($user),
            ];
        }catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function getCashbackReceivedValue()
    {
        return number_format(
            intval(
                Cashback::where(
                    "user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId()
                )->sum("value")
            ) / 100,
            2,
            ",",
            "."
        );
    }

    public function getAccountHealth(Request $request)
    {
        try {
            if (!$request->has("company")) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde",
                    ],
                    400
                );
            }
            $values = $this->getDataAccountHealth($request->company);

            return response()->json($values, 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    private function getDataAccountHealth($companyHash)
    {
        try {
            if (empty($companyHash)) {
                return [];
            }

            $companyId = current(Hashids::decode($companyHash));
            $company = Company::query()->find($companyId);
            if (is_null($company)) {
                return [];
            }

            $user = $company->user;

            return [
                "level" => $user->level,
                "account_score" => $user->account_score > 1 ? round($user->account_score, 1) : $user->account_score,
                "chargeback_score" =>
                    $user->chargeback_score > 1 ? round($user->chargeback_score, 1) : $user->chargeback_score,
                "attendance_score" =>
                    $user->attendance_score > 1 ? round($user->attendance_score, 1) : $user->attendance_score,
                "tracking_score" => $user->tracking_score > 1 ? round($user->tracking_score, 1) : $user->tracking_score,
            ];
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }

    public function getAccountChargeback(Request $request)
    {
        try {
            if (!$request->has("company")) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde",
                    ],
                    400
                );
            }

            $values = $this->getDataAccountChargeback($request->company);
            return response()->json($values, 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    private function getDataAccountChargeback($companyHash)
    {
        try {
            if (empty($companyHash)) {
                return [];
            }

            $companyModel = new Company();
            $companyId = current(Hashids::decode($companyHash));
            $company = $companyModel->find($companyId);
            $user = $company->user;

            if (empty($company)) {
                return [];
            }

            $startDate = now()
                ->startOfDay()
                ->subDays(150);
            $endDate = now()
                ->endOfDay()
                ->subDays(20);

            $chargebackService = new ChargebackService();
            $totalContestations = $chargebackService->getTotalContestationsInPeriod($user, $startDate, $endDate);

            $saleService = new SaleService();
            $totalApprovedSales = $saleService->getCreditCardApprovedSalesInPeriod($user, $startDate, $endDate);
            $contestationsRate = $totalApprovedSales ? round(($totalContestations / $totalApprovedSales) * 100, 2) : 0;
            return [
                "chargeback_score" =>
                    $user->chargeback_score > 1 ? round($user->chargeback_score, 1) : $user->chargeback_score,
                "chargeback_rate" => $contestationsRate ?? "0.00%",
                "total_sales_approved" => $totalApprovedSales ?? 0,
                "total_sales_chargeback" => $totalContestations ?? 0,
            ];
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function getAccountAttendance(Request $request)
    {
        try {
            if (!$request->has("company")) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde",
                    ],
                    400
                );
            }
            $values = $this->getDataAccountAttendance($request->company);

            return response()->json($values, 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    private function getDataAccountAttendance($companyHash)
    {
        try {
            if (empty($companyHash)) {
                return [];
            }
            $companyModel = new Company();
            $ticketsModel = new Ticket();
            $companyId = current(Hashids::decode($companyHash));
            $company = $companyModel->find($companyId);
            $user = $company->user;
            $userId = auth()->user()->account_owner_id;

            if (empty($company)) {
                return [];
            }

            //Tickets
            $statusArray = [
                $ticketsModel->present()->getTicketStatusEnum("open"),
                $ticketsModel->present()->getTicketStatusEnum("closed"),
                $ticketsModel->present()->getTicketStatusEnum("mediation"),
            ];

            $tickets = $ticketsModel
                ->selectRaw(
                    "count(*) as total,
                 sum(case when ticket_status_enum = ? then 1 else 0 end) as open,
                 sum(case when ticket_status_enum = ? then 1 else 0 end) as closed,
                 sum(case when ticket_status_enum = ? then 1 else 0 end) as mediation",
                    $statusArray
                )
                ->join("sales", "tickets.sale_id", "=", "sales.id")
                ->where("sales.owner_id", $userId)
                ->first();

            return [
                "attendance_score" =>
                    $user->attendance_score > 1 ? round($user->attendance_score, 1) : $user->attendance_score,
                "attendance_average_response_time" => $user->attendance_average_response_time,
                "total" => $tickets->total,
                "open" => $tickets->open,
                "closed" => $tickets->closed,
                "mediation" => $tickets->mediation,
            ];
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function getAccountTracking(Request $request)
    {
        try {
            if (!$request->has("company")) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde",
                    ],
                    400
                );
            }
            $values = $this->getDataAccountTracking($request->company);
            return response()->json($values, 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    private function getDataAccountTracking($companyHash)
    {
        try {
            if (empty($companyHash)) {
                return [];
            }
            $companyModel = new Company();
            $saleModel = new Sale();
            $companyId = current(Hashids::decode($companyHash));
            $company = $companyModel->find($companyId);
            $user = $company->user;
            $userId = auth()->user()->account_owner_id;

            if (empty($company)) {
                return [];
            }

            //Trackings
            $trackingPresenter = (new Tracking())->present();
            $trackingSystemStatus = [
                $trackingPresenter->getSystemStatusEnum("no_tracking_info"),
                $trackingPresenter->getSystemStatusEnum("unknown_carrier"),
                $trackingPresenter->getSystemStatusEnum("posted_before_sale"),
                $trackingPresenter->getSystemStatusEnum("duplicated"),
            ];
            $trackingSystemStatus = implode(",", $trackingSystemStatus);

            $trackingsInfo = DB::table("products_plans_sales as pps")
                ->selectRaw(
                    "count(*) as total,
                                               ifnull(sum(if(t.id is null, 1, 0)), 0) as unknown,
                                               ifnull(sum(if(t.system_status_enum in ({$trackingSystemStatus}), 1, 0)), 0) as problem,
                                               ifnull(ceil(avg(timestampdiff(day, s.end_date, t.created_at))), 0) as average_post_time,
                                               ifnull(max(if(t.id is null, timestampdiff(day, s.end_date, now()), 0)), 0) as oldest_sale"
                )
                ->join("sales as s", "s.id", "=", "pps.sale_id")
                ->leftJoin("trackings as t", "t.product_plan_sale_id", "=", "pps.id")
                ->leftJoin("products as p", "p.id", "=", "pps.product_id")
                ->where("s.owner_id", $userId)
                ->where("s.status", $saleModel->present()->getStatus("approved"))
                ->where("p.type_enum", (new Product())->present()->getType("physical"))
                ->first();

            $trackingsInfo->unknown_percentage = $trackingsInfo->total
                ? number_format(($trackingsInfo->unknown / $trackingsInfo->total) * 100, 2)
                : "0.00";
            $trackingsInfo->problem_percentage = $trackingsInfo->total
                ? number_format(($trackingsInfo->problem / $trackingsInfo->total) * 100, 2)
                : "0.00";

            return [
                "tracking_score" => $user->tracking_score > 1 ? round($user->tracking_score, 1) : $user->tracking_score,
                "average_post_time" => $trackingsInfo->average_post_time,
                //'oldest_sale'        => $trackingsInfo->oldest_sale,
                "problem" => $trackingsInfo->problem,
                "problem_percentage" => $trackingsInfo->problem_percentage,
                "unknown" => $trackingsInfo->unknown,
                "unknown_percentage" => $trackingsInfo->unknown_percentage,
                "tracking_today" => TrackingService::getTrackingToday($user)->count(),
                //'trackings'          => $trackingsInfo,
            ];
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function getAchievements()
    {
        try {
            if (!empty(request()->cookie("isManagerUser"))) {
                return \response()->json(
                    [
                        "message" => "Onboarding já lido",
                        "read" => true,
                    ],
                    Response::HTTP_OK
                );
            }

            $user = auth()->user();

            if (!($user->id == $user->account_owner_id)) {
                return \response()->json([
                    "message" => "Usuário não é o dono da conta",
                ]);
            }

            $dashboardNotifications = DashboardNotification::where([
                "user_id" => $user->id,
                "read_at" => null,
            ])
                ->whereIn("subject_type", [UpdateUserLevel::class, UpdateUserAchievements::class])
                ->get(["id", "subject_id", "subject_type"]);

            if (!empty($dashboardNotifications)) {
                return DashboardAchievementsResource::collection($dashboardNotifications);
            }

            return \response()->json([
                "message" => "Usuário não tem novas conquistas",
            ]);
        } catch (Exception $exception) {
            report($exception);

            return \response()->json(
                [
                    "message" => "Ocorreu um erro",
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function updateAchievements($achievement)
    {
        try {
            $idAchievement = \hashids()->decode($achievement);

            if (!DashboardNotification::find($idAchievement)) {
                return \response()->json(
                    [
                        "message" => "Conquista não encontrada !",
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            DashboardNotification::where("id", $idAchievement)->update([
                "read_at" => Carbon::now(),
            ]);

            return \response()->json(
                [
                    "message" => "Conquista atualizada !",
                ],
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            report($exception);

            return \response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a conquista",
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function verifyPixOnboarding()
    {
        try {
            if (!empty(request()->cookie("isManagerUser")) || Agent::isMobile()) {
                return \response()->json(
                    [
                        "message" => "Onboarding já lido",
                        "read" => true,
                    ],
                    Response::HTTP_OK
                );
            }

            $user = auth()->user();
            $userName = ucfirst(strtolower(current(explode(" ", $user->name))));

            $notfication = DashboardNotification::firstOrCreate([
                "user_id" => $user->id,
                "subject_id" => 1,
                "subject_type" => DashboardApiController::class."/verifyPixOnboarding",
            ]);

            if (!empty($notfication->read_at)) {
                return \response()->json(
                    [
                        "message" => "Onboarding já lido",
                        "read" => true,
                    ],
                    Response::HTTP_OK
                );
            }

            $userId = Hashids::connection("login")->encode($user->id);
            $expiration = Hashids::encode(
                Carbon::now()
                    ->addMinute()
                    ->unix()
            );
            $urlAuth = env("ACCOUNT_FRONT_URL")."/redirect/".$userId."/".(string) $expiration;

            return \response()->json(
                [
                    "message" => "Onboarding não lido",
                    "read" => false,
                    "onboarding" => \hashids()->encode($notfication->id),
                    "name" => $userName,
                    "accounts_url" => $urlAuth,
                ],
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            report($exception);

            return \response()->json(
                [
                    "message" => "Ocorreu um erro ao verificar o onboarding",
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function updatePixOnboarding($onboarding)
    {
        try {
            $onboardingId = \hashids()->decode($onboarding);

            DashboardNotification::where("id", $onboardingId)->update([
                "read_at" => Carbon::now(),
            ]);

            return \response()->json(
                [
                    "message" => "Onboarding atualizado !",
                ],
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            report($exception);

            return \response()->json(
                [
                    "message" => "Ocorreu um erro no update do  onboarding",
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function getUserTerms()
    {
        try {
            $user = auth()->user();
            $cloudfox = explode("azcend.com.br", $user->email);
            if (count($cloudfox) == 2) {
                return \response()->json(["message" => false], Response::HTTP_OK);
            }

            if ($user->company_default == Company::DEMO_ID) {
                Config::set("database.default", "mysql");
            }
            $userTermV2 = UserTerms::where([
                "user_id" => auth()->user()->id,
                "term_version" => "v2",
            ])->first();

            if ($user->company_default == Company::DEMO_ID) {
                Config::set("database.default", "demo");
            }
            if (empty($userTermV2)) {
                return \response()->json(["message" => true], Response::HTTP_OK);
            } else {
                return \response()->json(["message" => false], Response::HTTP_OK);
            }
        } catch (Exception $exception) {
            $user = auth()->user();
            if ($user->company_default == Company::DEMO_ID) {
                Config::set("database.default", "demo");
            }
            report($exception);
            return \response()->json(["message" => "Ocorreu um erro"], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateUserTerms()
    {
        try {
            $user = auth()->user();

            $cloudfox = explode("azcend.com.br", $user->email);
            if (count($cloudfox) == 2) {
                return \response()->json(["message" => true], Response::HTTP_OK);
            }

            if ($user->company_default == Company::DEMO_ID) {
                Config::set("database.default", "mysql");
            }

            $userTermV2 = UserTerms::where([
                "user_id" => auth()->user()->id,
                "term_version" => "v2",
            ])->first();

            if (empty($userTermV2)) {
                $geoIp = null;
                try {
                    $geoIp = geoip()->getLocation(IpService::getRealIpAddr());
                } catch (Exception $e) {
                    report($e);
                }

                $operationalSystem = Agent::platform();
                $browser = Agent::browser();

                $deviceData = [
                    "operational_system" => Agent::platform(),
                    "operation_system_version" => Agent::version($operationalSystem),
                    "browser" => Agent::browser(),
                    "browser_version" => Agent::version($browser),
                    "is_mobile" => Agent::isMobile(),
                    "ip" => @$geoIp["ip"],
                    "country" => @$geoIp["country"],
                    "city" => @$geoIp["city"],
                    "state" => @$geoIp["state"],
                    "state_name" => @$geoIp["state_name"],
                    "zip_code" => @$geoIp["postal_code"],
                    "currency" => @$geoIp["currency"],
                    "lat" => @$geoIp["lat"],
                    "lon" => @$geoIp["lon"],
                ];

                $userTermV2 = new UserTerms();
                $userTermsCreated = $userTermV2->create([
                    "user_id" => auth()->user()->id,
                    "term_version" => "v2",
                    "device_data" => json_encode($deviceData),
                    "accepted_at" => Carbon::now(),
                ]);

                if ($user->company_default == Company::DEMO_ID) {
                    Config::set("database.default", "demo");
                }

                if ($userTermsCreated) {
                    return \response()->json(["message" => true], Response::HTTP_OK); // Salvo com Sucesso
                }
                return \response()->json(["message" => false], Response::HTTP_OK);
            }
        } catch (Exception $exception) {
            $user = auth()->user();
            if ($user->company_default == Company::DEMO_ID) {
                Config::set("database.default", "demo");
            }

            report($exception);
            return \response()->json(
                ["message" => "Ocorreu um erro => ".$exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
