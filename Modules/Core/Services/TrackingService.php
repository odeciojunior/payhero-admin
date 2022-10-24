<?php

namespace Modules\Core\Services;

use App\Jobs\RevalidateTrackingDuplicateJob;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;
use Modules\Core\Events\ReportanaTrackingEvent;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{
    public function sendTrackingToApi($trackingCode)
    {
        if (!empty($trackingCode)) {
            $trackingmoreService = new TrackingmoreService();
            return $trackingmoreService->createTracking($trackingCode);
        } else {
            return null;
        }
    }

    public function findTrackingApi(Tracking $tracking)
    {
        $trackingmoreService = new TrackingmoreService();

        $trackingCode = $tracking->tracking_code;

        return $trackingmoreService->find($trackingCode);
    }

    public function getCheckpointsApi(Tracking $tracking, $apiTracking)
    {
        $checkpoints = collect();

        $apiCheckpoints = array_reverse($apiTracking->origin_info->trackinfo ?? []);
        $apiCheckpoints += array_reverse($apiTracking->destination_info->trackinfo ?? []);

        if (!empty($apiCheckpoints)) {
            foreach ($apiCheckpoints as $log) {
                $event = $log->Details ? $log->StatusDescription . " - " . $log->Details : $log->StatusDescription;

                if (!empty($event)) {
                    $status_enum = $this->parseStatusApi($log->checkpoint_status ?? "");
                    $status = $status_enum
                        ? __(
                            "definitions.enum.tracking.tracking_status_enum." .
                                $tracking->present()->getTrackingStatusEnum($status_enum)
                        )
                        : "Não informado";

                    //remove caracteres chineses e informações indesejadas
                    $blacklistWords = [
                        "asendia",
                        "beijing",
                        "chaozhou",
                        "china",
                        "dianhua",
                        "dongguan",
                        "fuyang",
                        "guangdongshengguangzhoushi",
                        "hang kong",
                        "hong kong",
                        "hongkong",
                        "jangxi",
                        "jinhua",
                        "jiangxi",
                        "jingwaijinkou",
                        "kulitiba",
                        "nanchang",
                        "shanghai",
                        "shantou",
                        "shanzhao",
                        "sheng",
                        "shenzhen",
                        "singapore",
                        "sunyou",
                        "xinyu",
                        "yanwen",
                        "yingshangxian",
                        "yiwu",
                        "zhongxin",
                    ];

                    if (
                        Str::contains(strtolower($event), $blacklistWords) ||
                        preg_match("/[^\p{Common}\p{Latin}]+/u", $event)
                    ) {
                        $event = "Encomenda em movimentação no exterior";
                    }

                    $checkpoints->add([
                        "tracking_status_enum" => $status_enum,
                        "tracking_status" => $status,
                        "created_at" => Carbon::parse($log->Date)->format("d/m/Y"),
                        "event" => $event,
                    ]);
                }
            }
        }

        return $checkpoints;
    }

    public function parseStatusApi($status)
    {
        $trackingmoreService = new TrackingmoreService();

        return $trackingmoreService->parseStatus($status);
    }

    private function getSystemStatus(string $trackingCode, ?object $apiResult, ProductPlanSale $productPlanSale)
    {
        $systemStatusEnum = Tracking::SYSTEM_STATUS_VALID;
        if (!empty($apiResult)) {
            //verifica se a data de postagem na transportadora é menor que a data da venda
            if (!empty($apiResult->origin_info->trackinfo ?? [])) {
                $postDate = Carbon::parse($apiResult->origin_info->ItemReceived);
                if ($postDate->lt($productPlanSale->created_at)) {
                    $systemStatusEnum = Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE;
                }
            } elseif ($apiResult->status !== "delivered") {
                $systemStatusEnum = Tracking::SYSTEM_STATUS_NO_TRACKING_INFO;
            }
        } else {
            $systemStatusEnum = Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER;
        }

        $saleId = $productPlanSale->sale_id;
        $upsellId = $productPlanSale->upsell_id;
        $customerId = $productPlanSale->customer_id;
        $deliveryId = $productPlanSale->delivery_id;

        //verifica se já tem uma venda nessa conta com o mesmo código de rastreio
        $duplicatedQuery = DB::table("sales as d")
            ->join("trackings as t", "d.id", "=", "t.sale_id")
            ->where("t.tracking_code", $trackingCode)
            ->where("t.system_status_enum", "!=", Tracking::SYSTEM_STATUS_DUPLICATED)
            ->where("d.id", "!=", $saleId)
            ->where(function ($query) use ($saleId) {
                $query->whereNull("d.upsell_id")->orWhere("d.upsell_id", "!=", $saleId);
            });

        if (!empty($upsellId)) {
            $duplicatedQuery->where("d.id", "!=", $upsellId)->where(function ($query) use ($upsellId) {
                $query->whereNull("d.upsell_id")->orWhere("d.upsell_id", "!=", $upsellId);
            });
        }

        $duplicatedQuery
            ->where(function ($query) use ($deliveryId, $customerId) {
                $query->where("d.customer_id", "!=", $customerId)->orWhere("d.delivery_id", "!=", $deliveryId);
            })
            ->whereIn("d.status", [Sale::STATUS_APPROVED, Sale::STATUS_IN_DISPUTE]);

        if ($duplicatedQuery->exists()) {
            $systemStatusEnum = Tracking::SYSTEM_STATUS_DUPLICATED;
        }

        return $systemStatusEnum;
    }

    public function updateTracking(string $trackingId, string $trackingCode)
    {
        try {
            $trackingIdDecode = current(Hashids::decode($trackingId));

            $tracking = Tracking::where("id", $trackingIdDecode)->first();

            if (!empty($tracking)) {
                if ($tracking->tracking_status_enum == Tracking::STATUS_DELIVERED) {
                    return false;
                }

                $oldTrackingCode = $tracking->tracking_code;

                $productPlanSale = ProductPlanSale::select([
                    "products_plans_sales.id",
                    "products_plans_sales.sale_id",
                    "products_plans_sales.product_id",
                    "products_plans_sales.amount",
                    "products_plans_sales.created_at",
                    "s.delivery_id",
                    "s.customer_id",
                    "s.upsell_id",
                ])
                    ->join("sales as s", "products_plans_sales.sale_id", "=", "s.id")
                    ->find($tracking->product_plan_sale_id);

                $apiResult = $this->sendTrackingToApi($trackingCode);
                $statusEnum = $this->parseStatusApi($apiResult->status ?? "");
                $systemStatusEnum = $this->getSystemStatus($trackingCode, $apiResult, $productPlanSale);

                $tracking->fill([
                    "tracking_code" => $trackingCode,
                    "tracking_status_enum" => $statusEnum,
                    "system_status_enum" => $systemStatusEnum,
                ]);

                if ($tracking->isDirty()) {
                    $tracking->save();
                    event(new CheckSaleHasValidTrackingEvent($tracking->sale_id));
                }

                if (strtoupper($oldTrackingCode) !== strtoupper($trackingCode)) {
                    //verifica se existem duplicatas do antigo código
                    $duplicates = Tracking::select("product_plan_sale_id as id")
                        ->where("tracking_code", $oldTrackingCode)
                        ->get();
                    //caso existam recria/revalida os códigos
                    if ($duplicates->isNotEmpty()) {
                        RevalidateTrackingDuplicateJob::dispatch($oldTrackingCode, $duplicates->toArray());
                    }
                }

                return $tracking;
            }

            return false;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    public function deleteTracking($trackingId)
    {
        try {
            $tracking = Tracking::find(current(Hashids::decode($trackingId)));

            switch (true) {
                case preg_match('/^\d{14}$/', $tracking->tracking_code):
                    $carrierCode = "dpd-brazil"; //jadlog
                    break;
                case preg_match('/^[A-Z]{2}\d{9}BR$/', $tracking->tracking_code):
                    $carrierCode = "brazil-correios";
                    break;
                case preg_match("/^LP00516\d{9}/", $tracking->tracking_code):
                    $carrierCode = "ltexp";
                    break;
                default:
                    $carrierCode = "cainiao";
                    break;
            }

            $trackingmoreService = new TrackingmoreService();

            $trackingDeleteService = $trackingmoreService->delete($carrierCode, $tracking->tracking_code);
            if ($trackingDeleteService) {
                return $tracking->delete();
            }

            return false;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    public function createOrUpdateTracking(
        string $trackingCode,
        int $productPlanSaleId,
        bool $logging = false,
        bool $notify = true,
        bool $checkDuplicates = true
    ): ?Tracking {
        try {
            $logging ? activity()->enableLogging() : activity()->disableLogging();

            $trackingCode = preg_replace("/[^a-zA-Z0-9]/", "", $trackingCode);
            $trackingCode = strtoupper($trackingCode);

            $productPlanSale = ProductPlanSale::select([
                "products_plans_sales.id",
                "products_plans_sales.sale_id",
                "products_plans_sales.product_id",
                "products_plans_sales.amount",
                "products_plans_sales.created_at",
                "s.delivery_id",
                "s.customer_id",
                "s.upsell_id",
            ])
                ->join("sales as s", "products_plans_sales.sale_id", "=", "s.id")
                ->find($productPlanSaleId);

            $apiResult = $this->sendTrackingToApi($trackingCode);

            $statusEnum = $this->parseStatusApi($apiResult->status ?? "");

            $systemStatusEnum = $this->getSystemStatus($trackingCode, $apiResult, $productPlanSale);

            $commonAttributes = [
                "sale_id" => $productPlanSale->sale_id,
                "product_id" => $productPlanSale->product_id,
                "product_plan_sale_id" => $productPlanSale->id,
                "amount" => $productPlanSale->amount,
                "delivery_id" => $productPlanSale->delivery_id,
            ];

            $newAttributes = [
                "tracking_code" => $trackingCode,
                "tracking_status_enum" => $statusEnum,
                "system_status_enum" => $systemStatusEnum,
            ];

            $tracking = Tracking::where($commonAttributes)->first();

            //atualiza e faz outras verificações caso já exista
            if (!empty($tracking)) {
                $oldTracking = (object) $tracking->getAttributes();
                $oldTrackingCode = $oldTracking->tracking_code;

                //atualiza
                $tracking->fill($newAttributes);
                if ($tracking->isDirty()) {
                    $tracking->save();
                    event(new CheckSaleHasValidTrackingEvent($productPlanSale->sale_id));
                }

                if (strtoupper($oldTrackingCode) != strtoupper($trackingCode) && $checkDuplicates) {
                    //verifica se existem duplicatas do antigo código
                    $duplicates = Tracking::select("product_plan_sale_id as id")
                        ->where("tracking_code", $oldTrackingCode)
                        ->get();
                    //caso existam recria/revalida os códigos
                    if ($duplicates->isNotEmpty()) {
                        RevalidateTrackingDuplicateJob::dispatch($oldTrackingCode, $duplicates->toArray());
                    }
                } else {
                    $notify = false;
                }
            } else {
                //senão cria um novo tracking
                $tracking = Tracking::updateOrCreate($commonAttributes + $newAttributes);
                event(new CheckSaleHasValidTrackingEvent($productPlanSale->sale_id));
            }

            if (!empty($tracking)) {
                if ($notify) {
                    event(new TrackingCodeUpdatedEvent($tracking->id));
                }
                event(new ReportanaTrackingEvent($productPlanSale->sale_id));
            }

            return $tracking;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function getTrackingsQueryBuilder($filters, $userId = 0)
    {
        if (!$userId) {
            $userId = auth()
                ->user()
                ->getAccountOwnerId();
        }

        $companyId = Company::DEMO_ID;
        if (!empty($filters["company"])) {
            $companyId = hashids_decode($filters["company"]);
        } else {
            $companyId = DB::table("users")
                ->select("company_default")
                ->where("id", $userId)
                ->first()->company_default;
        }

        if (!empty($filters["transaction_status"])) {
            $filters["transaction_status"] = is_array($filters["transaction_status"])
                ? implode(",", $filters["transaction_status"])
                : $filters["transaction_status"];
        }

        $productPlanSales = ProductPlanSale::join("sales as s", function ($join) use ($userId, $filters, $companyId) {
            $join->on("s.id", "=", "products_plans_sales.sale_id")->whereNull("s.deleted_at");

            $saleStatus = [Sale::STATUS_APPROVED, Sale::STATUS_IN_DISPUTE];

            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);

            $join
                ->whereBetween("s.end_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->whereIn("s.status", $saleStatus)
                ->where("s.owner_id", $userId);

            if (!empty($filters["sale"])) {
                $saleId = hashids_decode($filters["sale"], "sale_id");
                $join->where("s.id", $saleId);
            }

            $projects = null;
            if (!empty($filters["project"]) && $filters["project"][0] != "") {
                explode(",", $filters["project"]);
            }

            $tokens = [];
            $projectIds = [];

            if (!empty($projects)) {
                foreach ($projects as $project) {
                    if (str_starts_with($project, "TOKEN")) {
                        array_push($tokens, hashids_decode(str_replace("TOKEN-", "", $project)));
                        continue;
                    }
                    array_push($projectIds, hashids_decode($project));
                }
            }

            if (count($projectIds) > 0 || count($tokens) > 0) {
                $join->where(function ($querySale) use ($projectIds, $tokens) {
                    $querySale->whereIn("s.project_id", $projectIds)->orWhereIn("s.api_token_id", $tokens);
                });
            }
        });

        //filtro transactions
        if (!empty($filters["transaction_status"])) {
            $productPlanSales->join("transactions as t", function ($join) use ($companyId, $filters) {
                $join
                    ->on("t.sale_id", "=", "s.id")
                    ->where("t.company_id", $companyId)
                    ->whereNull("t.deleted_at");

                $transactionPresenter = (new Transaction())->present();
                $filterTransaction = explode(",", $filters["transaction_status"]);
                $statusEnum = [];
                foreach ($filterTransaction as $item) {
                    if ($item != "blocked") {
                        $statusEnum[] = $transactionPresenter->getStatusEnum($item);
                    }
                }

                if (in_array("blocked", $filterTransaction)) {
                    $join
                        ->where("t.release_date", "<=", Carbon::now()->format("Y-m-d"))
                        ->where("t.tracking_required", true)
                        ->where("t.status_enum", Transaction::STATUS_PAID);
                    if (count($statusEnum) > 0) {
                        $join->orWhereIn("t.status_enum", $statusEnum);
                    }
                } else {
                    $join->whereIn("t.status_enum", $statusEnum);
                }
            });
        } else {
            $productPlanSales->leftJoin("transactions as t", function ($join) use ($companyId) {
                $join->whereIn("t.status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED]);
                $join->on("t.sale_id", "s.id")->where("t.company_id", $companyId);
            });
            $productPlanSales->whereNotNull("t.id");
        }

        $productPlanSales
            ->where("t.type", Transaction::TYPE_PRODUCER)
            ->whereNull("t.invitation_id")
            ->where("t.is_waiting_withdrawal", 0)
            ->whereNull("t.withdrawal_id");

        if ((!empty($filters["problem"]) && $filters["problem"] == 1) || !empty($filters["tracking_code"])) {
            $productPlanSales->join("trackings as t2", function ($leftJoin) use ($filters) {
                $leftJoin->on("t2.product_plan_sale_id", "=", "products_plans_sales.id")->whereNull("t2.deleted_at");

                if (!empty($filters["problem"]) && $filters["problem"] == 1) {
                    $leftJoin->whereIn("t2.system_status_enum", [
                        Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
                        Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
                        Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
                        Tracking::SYSTEM_STATUS_DUPLICATED,
                    ]);
                }

                if (!empty($filters["tracking_code"])) {
                    $leftJoin->where("t2.tracking_code", "like", "%" . $filters["tracking_code"] . "%");
                }
            });
        } else {
            $productPlanSales->leftJoin("trackings as t2", function ($leftJoin) use ($filters) {
                $leftJoin->on("t2.product_plan_sale_id", "=", "products_plans_sales.id")->whereNull("t2.deleted_at");
            });
        }

        if (!empty($filters["status"])) {
            $filters["status"] = is_array($filters["status"]) ? implode(",", $filters["status"]) : $filters["status"];

            $productPlanSales->where(function ($where) use ($filters) {
                $filterStatus = explode(",", $filters["status"]);

                $statusArray = array_reduce(
                    $filterStatus,
                    function ($carry, $item) {
                        if ($item !== "unknown") {
                            $carry[] = (new Tracking())->present()->getTrackingStatusEnum($item);
                        }
                        return $carry;
                    },
                    []
                );

                $where->whereIn("t2.tracking_status_enum", $statusArray);

                if (in_array("unknown", $filterStatus)) {
                    $where->orWhereNull("t2.id");
                }
            });
        }

        $productPlanSales
            ->leftJoin("products as p", function ($leftJoin) {
                $leftJoin
                    ->on("p.id", "=", "products_plans_sales.product_id")
                    ->where("p.type_enum", Product::TYPE_PHYSICAL);
            })
            ->leftJoin("products_sales_api as psa", function ($leftJoin) {
                $leftJoin
                    ->on("psa.id", "=", "products_plans_sales.products_sales_api_id")
                    ->where("psa.product_type", "physical_goods");
            })
            ->where(function ($where) {
                $where->whereNotNull("p.id")->orWhereNotNull("psa.id");
            });
        return $productPlanSales;
    }

    public function getPaginatedTrackings($filters)
    {
        return $this->getTrackingsQueryBuilder($filters)
            ->select([
                "products_plans_sales.id",
                "t2.id as tracking_id",
                "t2.tracking_code",
                "t2.tracking_status_enum",
                "t2.system_status_enum",
                "s.id as sale_id",
                "s.is_chargeback_recovered",
                "s.end_date as approved_date",
                DB::raw("ifnull(p.id, psa.id) as product_id"),
                DB::raw("ifnull(p.name, psa.name) as product_name"),
                "p.description as product_description",
                "products_plans_sales.amount as product_amount",
            ])
            ->orderBy("approved_date", "desc")
            ->paginate(10);
    }

    public function getResume($filters)
    {
        return $this->getTrackingsQueryBuilder($filters)
            ->selectRaw(
                "COUNT(*) as total,
                SUM(CASE WHEN t2.tracking_status_enum = " .
                    Tracking::STATUS_POSTED .
                    " THEN 1 ELSE 0 END) as posted,
                SUM(CASE WHEN t2.tracking_status_enum = " .
                    Tracking::STATUS_DISPATCHED .
                    " THEN 1 ELSE 0 END) as dispatched,
                SUM(CASE WHEN t2.tracking_status_enum = " .
                    Tracking::STATUS_DELIVERED .
                    " THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN t2.tracking_status_enum = " .
                    Tracking::STATUS_OUT_FOR_DELIVERY .
                    " THEN 1 ELSE 0 END) as out_for_delivery,
                SUM(CASE WHEN t2.tracking_status_enum = " .
                    Tracking::STATUS_EXCEPTION .
                    " THEN 1 ELSE 0 END) as exception,
                SUM(CASE WHEN t2.id is null THEN 1 ELSE 0 END) as unknown"
            )
            ->first()
            ->toArray();
    }

    public function getAveragePostingTimeInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $approvedSalesWithTrackingCode = Tracking::select(
            DB::raw("ceil(avg(datediff(trackings.created_at, sales.end_date))) as averagePostingTime")
        )
            ->join("sales", "sales.id", "=", "trackings.sale_id")
            ->where("sales.payment_method", Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn("sales.status", [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE,
            ])
            ->whereBetween("sales.start_date", [
                $startDate->format("Y-m-d") . " 00:00:00",
                $endDate->format("Y-m-d") . " 23:59:59",
            ])
            ->where("sales.owner_id", $user->id)
            ->get();

        return $approvedSalesWithTrackingCode->toArray()[0]["averagePostingTime"] ?? null;
    }

    public function getUninformedTrackingCodeRateInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $saleService = new SaleService();
        $approvedSalesAmount = $saleService->getApprovedSalesInPeriod($user, $startDate, $endDate)->count();

        if ($approvedSalesAmount < 20) {
            return 7; //7% means score 6
        }

        $untrackedSalesAmount = $saleService
            ->getApprovedSalesInPeriod($user, $startDate, $endDate)
            ->doesntHave("tracking")
            ->count();

        return round(($untrackedSalesAmount * 100) / $approvedSalesAmount, 2);
    }

    public function getTrackingCodeProblemRateInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $salesWithTrackingCodeProblemsAmount = Sale::where(function ($query) {
            $query->whereHas("tracking", function ($trackingsQuery) {
                $status = [
                    Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
                    Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
                    Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
                    Tracking::SYSTEM_STATUS_DUPLICATED,
                ];
                $trackingsQuery->whereIn("system_status_enum", $status);
            });
        })
            ->where(function ($q) use ($user) {
                $q->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            })
            ->count();

        if ($salesWithTrackingCodeProblemsAmount < 20) {
            return 2; //2% means score 6
        }

        $saleService = new SaleService();
        $approvedSalesAmount = $saleService->getApprovedSalesInPeriod($user, $startDate, $endDate)->count();

        if (!$approvedSalesAmount) {
            return 0;
        }

        return round(($salesWithTrackingCodeProblemsAmount * 100) / $approvedSalesAmount, 2);
    }

    public static function getTrackingToday(User $user)
    {
        return Tracking::join("sales", "sales.id", "=", "trackings.sale_id")
            ->whereBetween("trackings.created_at", [
                now()->format("Y-m-d") . " 00:00:00",
                now()->format("Y-m-d") . " 23:59:59",
            ])
            ->where("sales.owner_id", $user->id)
            ->get();
    }
}
