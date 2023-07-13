<?php

namespace Modules\Core\Services\Reports;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\ProductSaleApi;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\BrazilStatesService;
use Vinkla\Hashids\Facades\Hashids;

class ReportMarketingService
{
    public function getColors($index = null, $hex = false)
    {
        $colors = ["blue", "purple", "pink", "orange", "yellow", "light-blue", "light-green", "grey"];

        if ($hex == true) {
            $colors = ["#2E85EC", "#FF7900", "#665FE8", "#F43F5E"];
        }

        if (!empty($index) || $index >= 0) {
            return $colors[$index];
        }

        return $colors;
    }

    public function getResumeMarketing($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "marketing-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId, $user) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $affiliate = DB::table("affiliates")
                ->select("id")
                ->where("user_id", $user->id)
                ->where("project_id", $projectId)
                ->whereNull("deleted_at")
                ->first();

            $checkoutsQr = Checkout::where("project_id", $projectId)->whereBetween("created_at", [
                $dateRange[0] . " 00:00:00",
                $dateRange[1] . " 23:59:59",
            ]);

            if (!empty($affiliate)) {
                $checkoutsQr = $checkoutsQr->where("affiliate_id", $affiliate->id);
            }

            $checkoutsCount = $checkoutsQr->count();

            $salesQr = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->where("owner_id", $ownerId)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("sales.project_id", $projectId);

            if (!empty($affiliate)) {
                $salesQr = $salesQr->where("sales.affiliate_id", $affiliate->id);
            }
            $salesCount = $salesQr->count();

            $salesValue = Transaction::join("sales", "sales.id", "transactions.sale_id")
                ->where("user_id", $ownerId)
                ->where("company_id", $filters["company_id"])
                ->where("project_id", $projectId)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->whereNull("invitation_id")
                ->whereIn("status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->sum("transactions.value");

            if ($checkoutsCount == 0 && $salesCount == 0 && $salesValue == 0) {
                return null;
            }

            return [
                "checkouts_count" => number_format($checkoutsCount, 0, ".", "."),
                "sales_count" => number_format($salesCount, 0, ".", "."),
                "sales_value" => foxutils()->formatMoney($salesValue / 100),
                "conversion" => !empty($checkoutsCount)
                    ? number_format(($salesCount * 100) / $checkoutsCount, 1, ".", ".") . "%"
                    : "0%",
            ];
        });
    }

    public function getSalesByState($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "sales-by-state-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $data = Sale::select(DB::raw("delivery.state, count(*) as sales_amount, SUM(transaction.value) as value"))
                ->join("transactions as transaction", function ($join) use ($ownerId) {
                    $join->on("transaction.sale_id", "=", "sales.id");
                    $join->where("transaction.user_id", $ownerId);
                })
                ->join("deliveries as delivery", function ($join) {
                    $join->on("delivery.id", "=", "sales.delivery_id");
                })
                ->where("transaction.company_id", $filters["company_id"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("value", ">", 0)
                ->where("sales.project_id", $projectId)
                ->groupBy("delivery.state")
                ->orderBy("value", "DESC")
                ->get()
                ->toArray();

            $totalValue = 0;
            $totalSales = 0;
            foreach ($data as $state) {
                $totalValue += $state["value"];
                $totalSales += $state["sales_amount"];
            }

            foreach ($data as $key => &$state) {
                if (empty(BrazilStatesService::getStatePopulation($state["state"]))) {
                    unset($data[$key]);
                    continue;
                }

                if ($filters["map_filter"] == "density") {
                    $salesPercentage =
                        ($state["sales_amount"] / BrazilStatesService::getStatePopulation($state["state"])) * 100000;
                    $state["percentage"] = number_format($salesPercentage, 2, ".", ".");
                } else {
                    $state["percentage"] = number_format(($state["value"] * 100) / $totalValue, 2, ".", ",") . "%";
                }
                $state["value"] = foxutils()->formatMoney($state["value"] / 100);
            }

            if ($filters["map_filter"] == "density") {
                $percentage = array_column($data, "percentage");
                array_multisort($percentage, SORT_DESC, $data);
            }
            $projectId = hashids_decode($filters["project_id"]);

            return $data;
        });
    }

    public function getMostFrequentSales($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;

        $cacheName = "frequent-sales-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));

            if (!$showFromApi) {
                $data = Plan::select(
                    DB::raw(
                        "plans.id, plans.name, plans.description, count(*) as sales_amount, cast(sum(plan_sale.plan_value) as unsigned) as value"
                    )
                )
                    ->with("products")
                    ->join("plans_sales as plan_sale", function ($join) {
                        $join->on("plan_sale.plan_id", "plans.id");
                    })
                    ->join("sales as sale", function ($join) {
                        $join->on("plan_sale.sale_id", "sale.id");
                    })
                    ->join("transactions as transaction", function ($join) {
                        $join->on("transaction.sale_id", "sale.id");
                    })
                    ->where("transaction.company_id", $filters["company_id"])
                    ->where("sale.status", Sale::STATUS_APPROVED)
                    ->whereIn("transaction.status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                    ->whereBetween("sale.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                    ->where("sale.project_id", $projectId)
                    ->where(
                        "transaction.user_id",
                        auth()
                            ->user()
                            ->getAccountOwnerId()
                    )
                    ->whereNull("invitation_id")
                    ->groupBy("plans.id")
                    ->orderBy("value", "DESC")
                    ->limit(10)
                    ->get();
            } else {
                $data = ProductSaleApi::join("sales", "products_sales_api.sale_id", "sales.id")
                    ->join("transactions as t", "t.sale_id", "=", "sales.id")
                    ->where("t.company_id", $filters["company_id"])
                    ->where("sales.status", Sale::STATUS_APPROVED)
                    ->whereIn("t.status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                    ->where("sales.api_token_id", $projectId)
                    ->whereBetween("sales.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                    ->select(
                        DB::raw(
                            'products_sales_api.name, products_sales_api.item_id, "" as description, "" as image, COUNT(*) as sales_amount, cast(sum(products_sales_api.price) as unsigned) as value'
                        )
                    )
                    ->groupBy("products_sales_api.item_id")
                    ->groupBy("products_sales_api.name")
                    ->orderBy("value", "DESC")
                    ->limit(8)
                    ->get();
            }

            if (count($data) == 0) {
                return null;
            }

            foreach ($data as &$plan) {
                $plan->photo = "https://azcend-digital-products.s3.amazonaws.com/admin/produto.svg";
                if (!$showFromApi) {
                    $plan->photo = $plan->products()->first()->photo;
                }
                $plan->sales_amount = number_format($plan->sales_amount, 0, ".", ".");
                $plan->value = foxutils()->formatMoney($showFromApi ? $plan->value / 100 : $plan->value);
            }

            return $data->toArray();
        });
    }

    public function getDevices($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "devices-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $data = Sale::selectRaw(
                "COUNT(*) AS total,
                        SUM(CASE WHEN checkout.is_mobile = 1 THEN 1 ELSE 0 END) AS count_mobile,
                        SUM(CASE WHEN checkout.is_mobile = 1 and sales.status = 1 THEN 1 ELSE 0 END) AS count_mobile_approved,
                        SUM(CASE WHEN checkout.is_mobile = 1 THEN transaction.value ELSE 0 END) AS value_mobile,
                        SUM(CASE WHEN checkout.is_mobile = 0 THEN 1 ELSE 0 END) AS count_desktop,
                        SUM(CASE WHEN checkout.is_mobile = 0 and sales.status = 1 THEN 1 ELSE 0 END) AS count_desktop_approved,
                        SUM(CASE WHEN checkout.is_mobile = 0 THEN transaction.value ELSE 0 END) AS value_desktop
                    "
            )
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->join("checkouts as checkout", function ($join) {
                    $join->on("sales.checkout_id", "=", "checkout.id");
                })
                ->join("transactions as transaction", function ($join) use ($ownerId) {
                    $join->on("transaction.sale_id", "=", "sales.id");
                    $join->where("transaction.user_id", $ownerId);
                })
                ->where("transaction.company_id", $filters["company_id"])
                ->where("owner_id", $ownerId)
                ->where("sales.project_id", $projectId)
                ->first()
                ->toArray();

            if (empty($data["count_mobile"]) && empty($data["count_desktop"])) {
                return null;
            }

            if (empty($data["count_mobile"])) {
                $data["count_mobile"] = 0;
                $data["count_mobile_approved"] = 0;
                $data["percentage_mobile"] = "0%";
                $data["percentage_mobile_total"] = "0%";
            } else {
                $data["percentage_mobile"] =
                    round(number_format(($data["count_mobile_approved"] * 100) / $data["count_mobile"], 2, ".", ",")) .
                    "%";
                $data["percentage_mobile_total"] =
                    round(number_format(($data["count_mobile"] * 100) / $data["total"], 2, ".", ",")) . "%";
            }

            if (empty($data["count_desktop"])) {
                $data["count_desktop"] = 0;
                $data["count_desktop_approved"] = 0;
                $data["percentage_desktop"] = "0%";
                $data["percentage_desktop_total"] = "0%";
            } else {
                $data["percentage_desktop"] =
                    round(
                        number_format(($data["count_desktop_approved"] * 100) / $data["count_desktop"], 2, ".", ",")
                    ) . "%";
                $data["percentage_desktop_total"] =
                    round(number_format(($data["count_desktop"] * 100) / $data["total"], 2, ".", ",")) . "%";
            }

            $data["conversion_mobile"] =
                $data["count_mobile_approved"] > 0 || $data["count_desktop_approved"] > 0
                    ? round(
                            number_format(
                                ($data["count_mobile_approved"] * 100) /
                                    ($data["count_mobile_approved"] + $data["count_desktop_approved"]),
                                2,
                                ".",
                                ","
                            )
                        ) . "%"
                    : "0%";
            $data["conversion_desktop"] =
                $data["count_mobile_approved"] > 0 || $data["count_desktop_approved"] > 0
                    ? round(
                            number_format(
                                ($data["count_desktop_approved"] * 100) /
                                    ($data["count_mobile_approved"] + $data["count_desktop_approved"]),
                                2,
                                ".",
                                ","
                            )
                        ) . "%"
                    : "0%";

            $data["value_mobile"] =
                $data["value_mobile"] > 0 ? foxutils()->formatMoney($data["value_mobile"] / 100) : 'R$ 0,00';
            $data["value_desktop"] =
                $data["value_desktop"] > 0 ? foxutils()->formatMoney($data["value_desktop"] / 100) : 'R$ 0,00';

            return [
                "mobile" => [
                    "total" => $data["count_mobile"],
                    "approved" => $data["count_mobile_approved"],
                    "value" => $data["value_mobile"],
                    "percentage_approved" => $data["percentage_mobile"],
                    "percentage_conversion" => $data["conversion_mobile"],
                    "percentage_total" => $data["percentage_mobile_total"],
                ],
                "desktop" => [
                    "total" => $data["count_desktop"],
                    "approved" => $data["count_desktop_approved"],
                    "value" => $data["value_desktop"],
                    "percentage_approved" => $data["percentage_desktop"],
                    "percentage_conversion" => $data["conversion_desktop"],
                    "percentage_total" => $data["percentage_desktop_total"],
                ],
            ];
        });
    }

    public function getOperationalSystems($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;

        $cacheName = "operational-systems-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $data = Checkout::select(DB::raw("os_enum, count(*) as sales_amount"))
                ->leftJoin("sales as s", "s.checkout_id", "=", "checkouts.id")
                ->where("s.status", Sale::STATUS_APPROVED)
                ->where(
                    "s.owner_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId()
                )
                ->whereBetween("s.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("checkouts.project_id", $projectId)
                ->groupBy("os_enum")
                ->orderBy("sales_amount", "desc")
                ->get()
                ->toArray();

            if (empty($data)) {
                return null;
            }

            $salesAmount = 0;

            foreach ($data as $key => &$operationalSystem) {
                if (
                    !in_array($operationalSystem["os_enum"], [
                        Checkout::OPERATIONAL_SYSTEM_ANDROID,
                        Checkout::OPERATIONAL_SYSTEM_IOS,
                        Checkout::OPERATIONAL_SYSTEM_WINDOWS,
                        Checkout::OPERATIONAL_SYSTEM_LINUX,
                    ])
                ) {
                    unset($data[$key]);
                    continue;
                }
                $salesAmount += $operationalSystem["sales_amount"];
            }

            foreach ($data as &$operationalSystem) {
                $operationalSystem["description"] = (new Checkout())
                    ->present()
                    ->getOperationalSystemName($operationalSystem["os_enum"]);
                $operationalSystem["percentage"] =
                    number_format(($operationalSystem["sales_amount"] * 100) / $salesAmount, 1, ".", ",") . "%";
                unset($operationalSystem["id_code"]);
                unset($operationalSystem["os_enum"]);
                unset($operationalSystem["sales_amount"]);
            }

            return $data;
        });
    }

    public function getStateDetail($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "state-detail-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $totalValue = Sale::join("transactions as transaction", function ($join) use ($ownerId) {
                $join->on("transaction.sale_id", "=", "sales.id");
                $join->where("transaction.user_id", $ownerId);
            })
                ->join("deliveries as delivery", function ($join) use ($filters) {
                    $join->on("delivery.id", "=", "sales.delivery_id")->where("delivery.state", $filters["state"]);
                })
                ->where("transaction.company_id", $filters["company_id"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("project_id", $projectId)
                ->where("owner_id", $ownerId)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->sum("transaction.value");

            $totalSales = Sale::join("deliveries as delivery", function ($join) use ($filters) {
                $join->on("delivery.id", "=", "sales.delivery_id")->where("delivery.state", $filters["state"]);
            })
                ->join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->where("project_id", $projectId)
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("owner_id", $ownerId)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->count();

            $accesses = Checkout::where("project_id", $projectId)
                ->whereBetween("created_at", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->count();

            return [
                "total_value" => foxutils()->formatMoney($totalValue / 100),
                "total_sales" => number_format($totalSales, 0, ".", "."),
                "accesses" => number_format($accesses, 0, ".", "."),
                "conversion" =>
                    $accesses > 0 ? number_format(($totalSales * 100) / $accesses, 1, ".", ",") . "%" : "0%",
            ];
        });
    }

    public function getResumeCoupons($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;

        $cacheName = "coupons-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 180, function () use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $coupons = Sale::select(DB::raw("sales.cupom_code as coupon, COUNT(*) as amount"))
                ->join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("sales.project_id", $projectId)
                ->where("sales.cupom_code", "<>", "")
                ->whereBetween("sales.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->groupBy("sales.cupom_code")
                ->orderByDesc("amount")
                ->limit(4)
                ->get();

            $total = 0;
            foreach ($coupons as $coupon) {
                $total += $coupon->amount;
            }

            $index = 0;
            foreach ($coupons as $coupon) {
                $coupon->percentage =
                    round(number_format(($coupon->amount * 100) / $total, 2, ".", ","), 1, PHP_ROUND_HALF_UP) . "%";
                $coupon->color = $this->getColors($index);
                $coupon->hexadecimal = $this->getColors($index, true);
                $index++;
            }

            $couponsArray = $coupons->toArray();

            return [
                "coupons" => $couponsArray,
                "total" => $total,
            ];
        });
    }

    public function getResumeRegions($filters)
    {
        $filters["company_id"] = Auth::user()->company_default;

        $cacheName = "regions-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = current(Hashids::decode($filters["project_id"]));

            $regions = Checkout::select(
                DB::raw('
                    ip_state as region,
                    COUNT(*) as access,
                    COUNT(CASE WHEN checkouts.status_enum in (4, 3) then 1 end) as conversion
                ')
            )
                ->leftJoin("sales as s", "s.checkout_id", "=", "checkouts.id")
                ->join("transactions as t", "t.sale_id", "=", "s.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("checkouts.created_at", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("checkouts.project_id", $projectId)
                ->whereNotNull("checkouts.ip_state")
                ->groupBy("region")
                ->having("conversion", ">", 0)
                ->orderBy("conversion", "desc")
                ->get()
                ->toArray();

            $brasilianStates = [
                "AC",
                "AL",
                "AP",
                "AM",
                "BA",
                "CE",
                "DF",
                "ES",
                "GO",
                "MA",
                "MT",
                "MS",
                "MG",
                "PA",
                "PB",
                "PR",
                "PE",
                "PI",
                "RJ",
                "RN",
                "RS",
                "RO",
                "RR",
                "SC",
                "SP",
                "SE",
                "TO",
            ];
            foreach ($regions as $key => &$region) {
                if (!in_array($region["region"], $brasilianStates)) {
                    unset($regions[$key]);
                    continue;
                }
                $region["percentage_conversion"] = round(
                    number_format(($region["conversion"] * 100) / $region["access"], 2, ".", ","),
                    1,
                    PHP_ROUND_HALF_UP
                );
            }

            $percentageConversion = array_column($regions, "percentage_conversion");
            array_multisort($percentageConversion, SORT_DESC, $regions);

            return $regions;
        });
    }

    public function getResumeOrigins($filters)
    {
        $projectId = hashids_decode($filters["project_id"]);

        $filters["company_id"] = Auth::user()->company_default;

        $userId = auth()
            ->user()
            ->getAccountOwnerId();
        $status = Sale::STATUS_APPROVED;
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $originsData = Sale::select(
            DB::raw(
                "count(*) as sales_amount, SUM(transaction.value) as value, checkout." .
                    $filters["origin"] .
                    " as origin"
            )
        )
            ->leftJoin("transactions as transaction", function ($join) use ($userId, $filters) {
                $join->on("transaction.sale_id", "=", "sales.id");
                $join->where("transaction.user_id", $userId);
                $join->where("transaction.company_id", $filters["company_id"]);
            })
            ->leftJoin("checkouts as checkout", function ($join) {
                $join->on("checkout.id", "=", "sales.checkout_id");
            })
            ->where("sales.status", $status)
            ->where("sales.project_id", $projectId)
            ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
            ->whereNotIn("checkout." . $filters["origin"], ["", "null"])
            ->whereNotNull("checkout." . $filters["origin"])
            ->groupBy("checkout." . $filters["origin"])
            ->orderBy("sales_amount", "DESC");

        return $originsData;
    }
}
