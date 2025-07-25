<?php

namespace Modules\Core\Services\Reports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductSaleApi;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

class ReportSaleService
{
    public function getResumeSales($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "sales-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $dateFilter = !empty($filters["status"]) && $filters["status"] == "approved" ? "end_date" : "start_date";
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));

            $sales = Sale::select("sales.*")
                ->join("transactions", "transactions.sale_id", "sales.id")
                ->where("transactions.company_id", $filters["company_id"])
                ->where("owner_id", $ownerId)
                ->whereBetween($dateFilter, [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId);

            if (!empty($filters["status"])) {
                $salesModel = new Sale();
                if ($filters["status"] === "others") {
                    $statusNotIn = [
                        Sale::STATUS_APPROVED,
                        Sale::STATUS_PENDING,
                        Sale::STATUS_CANCELED,
                        Sale::STATUS_REFUSED,
                        Sale::STATUS_REFUNDED,
                        Sale::STATUS_CHARGEBACK,
                    ];
                    $sales->whereNotIn("sales.status", $statusNotIn);
                } else {
                    $sales->where("sales.status", $salesModel->present()->getStatus($filters["status"]));
                }
            }

            if ($dateRange["0"] == $dateRange["1"]) {
                return $this->getResumeSalesByHours($sales, $filters);
            }

            if ($dateRange["0"] != $dateRange["1"]) {
                $startDate = Carbon::createFromFormat("Y-m-d", $dateRange["0"], "America/Sao_Paulo");
                $endDate = Carbon::createFromFormat("Y-m-d", $dateRange["1"], "America/Sao_Paulo");
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumeSalesByDays($sales, $filters);
                }
                if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumeSalesByTwentyDays($sales, $filters);
                }
                if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumeSalesByFortyDays($sales, $filters);
                }
                if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumeSalesByWeeks($sales, $filters);
                }
                if ($diffInDays > 140) {
                    return $this->getResumeSalesByMonths($sales, $filters);
                }
            }
        });
    }

    public function getResumeSalesByHours($sales, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        if (Carbon::parse($dateRange[0])->format("m/d/y") == Carbon::now()->format("m/d/y")) {
            $labelList = [];
            $currentHour = date("H");
            $startHour = 0;

            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . "h");
                $startHour++;
            }
        } else {
            $labelList = [
                "0h",
                "1h",
                "2h",
                "3h",
                "4h",
                "5h",
                "6h",
                "7h",
                "8h",
                "9h",
                "10h",
                "11h",
                "12h",
                "13h",
                "14h",
                "15h",
                "16h",
                "17h",
                "18h",
                "19h",
                "20h",
                "21h",
                "22h",
                "23h",
            ];
        }

        $resume = $sales->select(DB::raw("sales.id as sale, HOUR(start_date) as hour"))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round(($saleData[count($saleData) - 1] / $saleData[0] - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = "grey";
        if ($variation > 0) {
            $color = "green";
        } elseif ($variation < 0) {
            $color = "pink";
        }

        return [
            "chart" => [
                "labels" => $labelList,
                "values" => $saleData,
            ],
            "total" => number_format($resume->count(), 0, ".", "."),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeSalesByDays($sales, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $labelList = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format("d-m"));
            $dataFormated = $dataFormated->addDays(1);
        }

        $dateFilter = !empty($filters["status"]) && $filters["status"] == "approved" ? "end_date" : "start_date";
        $resume = $sales->select(DB::raw("sales.id as sale, DATE(sales.{$dateFilter}) as date"))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("d-m") == $label) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round(($saleData[count($saleData) - 1] / $saleData[0] - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = "grey";
        if ($variation > 0) {
            $color = "green";
        } elseif ($variation < 0) {
            $color = "pink";
        }

        return [
            "chart" => [
                "labels" => $labelList,
                "values" => $saleData,
            ],
            "total" => number_format($sales->count(), 0, ".", "."),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeSalesByTwentyDays($sales, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format("d/m"));
            $dataFormated = $dataFormated->addDays(2);
            if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                array_push($labelList, $dataFormated->format("d/m"));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format("d/m"));
                break;
            }
        }

        $dateFilter = !empty($filters["status"]) && $filters["status"] == "approved" ? "end_date" : "start_date";
        $resume = $sales->select(DB::raw("sales.id as sale, DATE({$dateFilter}) as date"))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if (
                    Carbon::parse($r->date)->format("d/m") == $label ||
                    Carbon::parse($r->date)
                        ->subdays(1)
                        ->format("d/m") == $label
                ) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round(($saleData[count($saleData) - 1] / $saleData[0] - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = "grey";
        if ($variation > 0) {
            $color = "green";
        } elseif ($variation < 0) {
            $color = "pink";
        }

        return [
            "chart" => [
                "labels" => $labelList,
                "values" => $saleData,
            ],
            "total" => number_format($resume->count(), 0, ".", "."),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeSalesByFortyDays($sales, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format("d/m"));
            $dataFormated = $dataFormated->addDays(3);
            if ($dataFormated->diffInDays($endDate) < 3) {
                array_push($labelList, $dataFormated->format("d/m"));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format("d/m"));
                break;
            }
        }

        $dateFilter = !empty($filters["status"]) && $filters["status"] == "approved" ? "end_date" : "start_date";
        $resume = $sales->select(DB::raw("sales.id as sale, DATE({$dateFilter}) as date"))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if (
                        Carbon::parse($r->date)
                            ->addDays($x)
                            ->format("d/m") == $label
                    ) {
                        $saleDataValue += 1;
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round(($saleData[count($saleData) - 1] / $saleData[0] - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = "grey";
        if ($variation > 0) {
            $color = "green";
        } elseif ($variation < 0) {
            $color = "pink";
        }

        return [
            "chart" => [
                "labels" => $labelList,
                "values" => $saleData,
            ],
            "total" => number_format($sales->count(), 0, ".", "."),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeSalesByWeeks($sales, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format("d/m"));
            $dataFormated = $dataFormated->addDays(7);
            if ($dataFormated->diffInDays($endDate) < 7) {
                array_push($labelList, $dataFormated->format("d/m"));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format("d/m"));
                break;
            }
        }

        $dateFilter = !empty($filters["status"]) && $filters["status"] == "approved" ? "end_date" : "start_date";
        $resume = $sales->select(DB::raw("sales.id as sale, DATE({$dateFilter}) as date"))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if (
                        Carbon::parse($r->date)
                            ->addDays($x)
                            ->format("d/m") == $label
                    ) {
                        $saleDataValue += 1;
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round(($saleData[count($saleData) - 1] / $saleData[0] - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = "grey";
        if ($variation > 0) {
            $color = "green";
        } elseif ($variation < 0) {
            $color = "pink";
        }

        return [
            "chart" => [
                "labels" => $labelList,
                "values" => $saleData,
            ],
            "total" => number_format($resume->count(), 0, ".", "."),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeSalesByMonths($sales, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format("m/y"));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $dateFilter = !empty($filters["status"]) && $filters["status"] == "approved" ? "end_date" : "start_date";
        $resume = $sales->select(DB::raw("sales.id as sale, DATE({$dateFilter}) as date"))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("m/y") == $label) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round(($saleData[count($saleData) - 1] / $saleData[0] - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = "grey";
        if ($variation > 0) {
            $color = "green";
        } elseif ($variation < 0) {
            $color = "pink";
        }

        return [
            "chart" => [
                "labels" => $labelList,
                "values" => $saleData,
            ],
            "total" => number_format($resume->count(), 0, ".", "."),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeTypePayments($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "payment-type-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);

            $query = Sale::join("transactions", "transactions.sale_id", "sales.id")
                ->where("transactions.user_id", $ownerId)
                ->where("transactions.company_id", $filters["company_id"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->selectRaw("SUM(transactions.value / 100) as total")
                ->selectRaw("SUM(IF(payment_method = 1, transactions.value / 100, 0)) as total_credit_card")
                ->selectRaw("SUM(IF(payment_method = 2, transactions.value / 100, 0)) as total_boleto")
                ->selectRaw("SUM(IF(payment_method = 4, transactions.value / 100, 0)) as total_pix")
                ->first();

            $total = $query->total;

            if ($total == 0) {
                return null;
            }

            $totalCreditCard = $query->total_credit_card;
            $percentageCreditCard =
                $totalCreditCard > 0 ? number_format(($totalCreditCard * 100) / $total, 2, ".", ",") : 0;

            $totalBoleto = $query->total_boleto;
            $percentageBoleto = $totalBoleto > 0 ? number_format(($totalBoleto * 100) / $total, 2, ".", ",") : 0;

            $totalPix = $query->total_pix;
            $percentagePix = $totalPix > 0 ? number_format(($totalPix * 100) / $total, 2, ".", ",") : 0;

            $data = [
                "boleto" => [
                    "value" => number_format($totalBoleto, 2, ",", "."),
                    "percentage" => round($percentageBoleto, 1, PHP_ROUND_HALF_UP) . "%",
                ],
                "pix" => [
                    "value" => number_format($totalPix, 2, ",", "."),
                    "percentage" => round($percentagePix, 1, PHP_ROUND_HALF_UP) . "%",
                ],
                "credit_card" => [
                    "value" => number_format($totalCreditCard, 2, ",", "."),
                    "percentage" => round($percentageCreditCard, 1, PHP_ROUND_HALF_UP) . "%",
                ],
            ];

            $value = [];
            foreach ($data as $val) {
                array_push($value, foxutils()->onlyNumbers($val["value"]));
            }
            array_multisort($value, SORT_DESC, $data);

            return $data;
        });
    }

    public function getResumeProducts($filters)
    {
        $filters["company_id"] = Auth::user()->company_default;

        $cacheName = "products-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);

            if (!$showFromApi) {
                $products = Product::join("products_plans_sales", "products.id", "products_plans_sales.product_id")
                    ->join("sales", "products_plans_sales.sale_id", "sales.id")
                    ->join("transactions as t", "t.sale_id", "=", "sales.id")
                    ->where("t.company_id", $filters["company_id"])
                    ->where("sales.status", Sale::STATUS_APPROVED)
                    ->where("sales.project_id", $projectId)
                    ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                    ->select(
                        DB::raw("products.name, products.description, products.photo as image, COUNT(*) as amount")
                    )
                    ->groupBy("products.id")
                    ->orderByDesc("amount")
                    ->limit(8)
                    ->get();
            } else {
                $products = ProductSaleApi::join("sales", "products_sales_api.sale_id", "sales.id")
                    ->join("transactions as t", "t.sale_id", "=", "sales.id")
                    ->where("t.company_id", $filters["company_id"])
                    ->where("sales.api_token_id", $projectId)
                    ->where("sales.status", Sale::STATUS_APPROVED)
                    ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                    ->select(
                        DB::raw(
                            'products_sales_api.name, products_sales_api.item_id, "" as description, "" as image, COUNT(*) as amount'
                        )
                    )
                    ->groupBy("products_sales_api.item_id")
                    ->groupBy("products_sales_api.name")
                    ->orderByDesc("amount")
                    ->limit(8)
                    ->get();
            }

            if (count($products) == 0) {
                return [
                    "products" => null,
                    "total" => 0,
                ];
            }

            $total = 0;
            foreach ($products as $r) {
                $total += $r->amount;
            }

            $firstValue = $products[0]["amount"];

            $index = 0;
            foreach ($products as $result) {
                $percentage = round(
                    number_format(($result->amount * 100) / $firstValue, 2, ".", ","),
                    1,
                    PHP_ROUND_HALF_UP
                );

                $result->image = empty($result->image)
                    ? "https://azcend-digital-products.s3.amazonaws.com/admin/produto.svg"
                    : $result->image;
                $result->percentage = $index == 0 ? "100%" : $percentage . "%";
                $result->color = $this->getColors($index);

                $index++;
            }

            $productsArray = $products->toArray();

            return [
                "products" => $productsArray,
                "total" => $total,
            ];
        });
    }

    public function getSalesResume($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "sales-balance-resume-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));

            $salesApproved = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("sales.end_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.owner_id", $ownerId)
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesAverageTicket = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("sales.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->avg("sales.original_total_paid_value");

            $salesComission = Transaction::join("sales", "sales.id", "transactions.sale_id")
                ->where("user_id", $ownerId)
                ->where("company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->whereNull("invitation_id")
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->whereIn("status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->sum("transactions.value");

            $salesChargeback = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("sales.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_CHARGEBACK)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->sum("sales.original_total_paid_value");

            return [
                "transactions" => number_format($salesApproved, 0, ".", "."),
                "average_ticket" => foxutils()->formatMoney($salesAverageTicket / 100),
                "comission" => foxutils()->formatMoney($salesComission / 100),
                "chargeback" => foxutils()->formatMoney($salesChargeback / 100),
            ];
        });
    }

    public function getSalesDistribuitions($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "sales-distribuition-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));

            $salesApprovedSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("owner_id", $ownerId)
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesPendingSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_PENDING)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesCanceledSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_CANCELED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesRefusedSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_REFUSED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesRefundedSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_REFUNDED)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesChargebackSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_CHARGEBACK)
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $salesOtherSum = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->whereNotIn("sales.status", [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_PENDING,
                    Sale::STATUS_CANCELED,
                    Sale::STATUS_REFUSED,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_CHARGEBACK,
                ])
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->count();

            $total =
                $salesApprovedSum +
                $salesPendingSum +
                $salesCanceledSum +
                $salesRefusedSum +
                $salesRefundedSum +
                $salesChargebackSum +
                $salesOtherSum;

            return [
                "total" => number_format($total, 0, ".", "."),
                "approved" => [
                    "amount" => number_format($salesApprovedSum, 0, ".", "."),
                    "percentage" => empty($total) ? 0 : number_format(($salesApprovedSum * 100) / $total, 2, ".", ","),
                ],
                "pending" => [
                    "amount" => number_format($salesPendingSum, 0, ".", "."),
                    "percentage" => empty($total) ? 0 : number_format(($salesPendingSum * 100) / $total, 2, ".", ","),
                ],
                "canceled" => [
                    "amount" => number_format($salesCanceledSum, 0, ".", "."),
                    "percentage" => empty($total) ? 0 : number_format(($salesCanceledSum * 100) / $total, 2, ".", ","),
                ],
                "refused" => [
                    "amount" => number_format($salesRefusedSum, 0, ".", "."),
                    "percentage" => empty($total) ? 0 : number_format(($salesRefusedSum * 100) / $total, 2, ".", ","),
                ],
                "refunded" => [
                    "amount" => number_format($salesRefundedSum, 0, ".", "."),
                    "percentage" => empty($total) ? 0 : number_format(($salesRefundedSum * 100) / $total, 2, ".", ","),
                ],
                "chargeback" => [
                    "amount" => number_format($salesChargebackSum, 0, ".", "."),
                    "percentage" => empty($total)
                        ? 0
                        : number_format(($salesChargebackSum * 100) / $total, 2, ".", ","),
                ],
                "other" => [
                    "amount" => number_format($salesOtherSum, 0, ".", "."),
                    "percentage" => empty($total) ? 0 : number_format(($salesOtherSum * 100) / $total, 2, ".", ","),
                ],
            ];
        });
    }

    public function getAbandonedCarts($filters)
    {
        $filters["company_id"] = Auth::user()->company_default;

        $cacheName = "abandoned-carts-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $checkoutsData = Checkout::select([
                DB::raw("SUM(CASE WHEN checkouts.status_enum = 2 THEN 1 ELSE 0 END) AS abandoned"),
                DB::raw("SUM(CASE WHEN checkouts.status_enum = 3 THEN 1 ELSE 0 END) AS recovered"),
            ])
                ->whereBetween("created_at", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("project_id", $projectId)
                ->first();

            $recoveredValue = Sale::join("checkouts as checkout", function ($join) {
                $join->on("sales.checkout_id", "=", "checkout.id");
                $join->where("checkout.status_enum", Checkout::STATUS_RECOVERED);
            })
                ->join("transactions as transaction", function ($join) {
                    $join->on("sales.id", "=", "transaction.sale_id");
                })
                ->where("sales.project_id", $projectId)
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->whereIn("transaction.status_enum", [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->where(
                    "transaction.user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId()
                )
                ->where("transaction.company_id", $filters["company_id"])
                ->whereNull("invitation_id")
                ->sum("transaction.value");

            return [
                "percentage" =>
                    $checkoutsData->abandoned > 0
                        ? number_format(($checkoutsData->recovered * 100) / $checkoutsData->abandoned, 1, ".", ",") .
                            "%"
                        : "0%",
                "value" => foxutils()->formatMoney($recoveredValue / 100),
            ];
        });
    }

    public function getOrderBump($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "order-bump-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $data = Transaction::select(DB::raw("count(*) as amount, sum(value) as value"))
                ->join("sales", function ($join) {
                    $join->on("transactions.sale_id", "sales.id");
                })
                ->where("company_id", $filters["company_id"])
                ->whereBetween("sales.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->where("sales.has_order_bump", true)
                ->where("sales.project_id", $projectId)
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("user_id", $ownerId)
                ->first();

            if (empty($data)) {
                return null;
            }

            return [
                "value" => foxutils()->formatMoney($data->value / 100),
                "amount" => $data->amount,
            ];
        });
    }

    public function getUpsell($filters)
    {
        $user = Auth::user();
        $filters["company_id"] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = "upsell-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters, $ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters["project_id"]);

            $data = Transaction::select(DB::raw("count(*) as amount, sum(value) as value"))
                ->join("sales", function ($join) {
                    $join->on("transactions.sale_id", "sales.id");
                })
                ->where("company_id", $filters["company_id"])
                ->whereBetween("sales.start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->whereNotNull("sales.upsell_id")
                ->where("sales.project_id", $projectId)
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->where("user_id", $ownerId)
                ->first();

            if (empty($data)) {
                return null;
            }

            return [
                "value" => foxutils()->formatMoney($data->value / 100),
                "amount" => $data->amount,
            ];
        });
    }

    public function getConversion($filters)
    {
        $filters["company_id"] = Auth::user()->company_default;

        $cacheName = "conversion-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters["project_id"], "TOKEN");
            $projectId = hashids_decode(str_replace("TOKEN-", "", $filters["project_id"]));

            $query = Sale::join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->whereBetween("start_date", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
                ->selectRaw(DB::raw("SUM(CASE WHEN payment_method = 1 THEN 1 ELSE 0 END) AS total_credit_card"))
                ->selectRaw(
                    DB::raw(
                        "SUM(CASE WHEN payment_method = 1 and sales.status = 1 THEN 1 ELSE 0 END) AS total_credit_card_approved"
                    )
                )
                ->selectRaw(DB::raw("SUM(CASE WHEN payment_method = 2 THEN 1 ELSE 0 END) AS total_boleto"))
                ->selectRaw(
                    DB::raw(
                        "SUM(CASE WHEN payment_method = 2 and sales.status = 1 THEN 1 ELSE 0 END) AS total_boleto_approved"
                    )
                )
                ->selectRaw(DB::raw("SUM(CASE WHEN payment_method = 4 THEN 1 ELSE 0 END) AS total_pix"))
                ->selectRaw(
                    DB::raw(
                        "SUM(CASE WHEN payment_method = 4 and sales.status = 1 THEN 1 ELSE 0 END) AS total_pix_approved"
                    )
                )
                ->where("sales.api_flag", $showFromApi)
                ->where($showFromApi ? "sales.api_token_id" : "sales.project_id", $projectId)
                ->first();

            if ($query->total_credit_card == 0 && $query->total_boleto == 0 && $query->total_pix == 0) {
                return null;
            }

            $totalCreditCard = $query->total_credit_card;
            $totalCreditCardApproved = $query->total_credit_card_approved;
            $percentageCreditCard =
                $totalCreditCard > 0
                    ? number_format(($totalCreditCardApproved * 100) / $totalCreditCard, 2, ".", ",")
                    : 0;

            $totalBoleto = $query->total_boleto;
            $totalBoletoApproved = $query->total_boleto_approved;
            $percentageBoleto =
                $totalBoleto > 0 ? number_format(($totalBoletoApproved * 100) / $totalBoleto, 2, ".", ",") : 0;

            $totalPix = $query->total_pix;
            $totalPixApproved = $query->total_pix_approved;
            $percentagePix = $totalPix > 0 ? number_format(($totalPixApproved * 100) / $totalPix, 2, ".", ",") : 0;

            return [
                "credit_card" => [
                    "total" => number_format($totalCreditCard, 0, ".", "."),
                    "approved" => number_format($totalCreditCardApproved, 0, ".", "."),
                    "percentage" => round($percentageCreditCard, 1, PHP_ROUND_HALF_UP) . "%",
                ],
                "boleto" => [
                    "total" => number_format($totalBoleto, 0, ".", "."),
                    "approved" => number_format($totalBoletoApproved, 0, ".", "."),
                    "percentage" => round($percentageBoleto, 1, PHP_ROUND_HALF_UP) . "%",
                ],
                "pix" => [
                    "total" => number_format($totalPix, 0, ".", "."),
                    "approved" => number_format($totalPixApproved, 0, ".", "."),
                    "percentage" => round($percentagePix, 1, PHP_ROUND_HALF_UP) . "%",
                ],
            ];
        });
    }

    public function getRecurrence($filters)
    {
        $filters["company_id"] = Auth::user()->company_default;

        $cacheName = "recurrency-" . json_encode($filters);
        return cache()->remember($cacheName, 300, function () use ($filters) {
            $showFromApi = $filters["project_id"] == "API-TOKEN";
            $projectId = $showFromApi ? null : hashids_decode($filters["project_id"]);

            date_default_timezone_set("America/Sao_Paulo");
            config()->set("database.connections.mysql.strict", false);
            DB::reconnect();

            $sales = Sale::select([
                DB::raw("YEAR(sales.start_date) as year"),
                DB::raw("MONTH(sales.start_date) as month"),
                DB::raw("count(*) as amount"),
                DB::raw(
                    "(select count(*) from sales as s where s.customer_id = sales.customer_id limit 1) as sales_count"
                ),
            ])
                ->join("transactions as t", "t.sale_id", "=", "sales.id")
                ->where("t.company_id", $filters["company_id"])
                ->where(
                    "sales.start_date",
                    ">",
                    now()
                        ->subMonths(6)
                        ->startOfMonth()
                )
                ->where("sales.api_flag", $showFromApi)
                ->having("sales_count", ">", 1)
                ->groupBy("year", "month");

            if (!$showFromApi) {
                $sales->where("sales.project_id", $projectId);
            }

            $sales = $sales->get();

            config()->set("database.connections.mysql.strict", true);
            DB::reconnect();

            $labels = [];
            $values = [];

            foreach ($sales as $sale) {
                array_push($labels, date("M", mktime(0, 0, 0, $sale->month, 0, 0)));
                array_push($values, $sale->sales_count);
            }

            $total = array_sum($values);

            return [
                "chart" => [
                    "labels" => $labels,
                    "values" => $values,
                ],
                "total" => $total,
            ];
        });
    }

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
}
