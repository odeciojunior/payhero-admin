<?php

namespace Modules\Core\Services\Reports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\Safe2PayService;

class ReportFinanceService
{
    public function getResumeCommissions($filters)
    {
        $user = Auth::user();
        $filters['company_id'] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = 'comissions-resume-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters,$ownerId){
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters['project_id'],'TOKEN');
            $projectId = hashids_decode(str_replace('TOKEN-','',$filters['project_id']));

            $transactions = Transaction::join('sales', 'sales.id', 'transactions.sale_id')
                            ->where('user_id', $ownerId)
                            ->where('company_id', $filters['company_id'])
                            ->where('sales.api_flag', $showFromApi)
                            ->where($showFromApi ? 'sales.api_token_id' : 'sales.project_id', $projectId)
                            ->whereBetween('sales.start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
                            ->whereNull('transactions.invitation_id')
                            ->whereIn('transactions.status_enum', [ Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID ])
                            ->whereNull('invitation_id');

            if ($transactions->count() == 0) {
                return null;
            }

            $date["startDate"] = $dateRange[0];
            $date["endDate"] = $dateRange[1];

            if ($date["startDate"] == $date["endDate"]) {
                return $this->getResumeCommissionsByHours($transactions, $filters);
            }
            if ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumeCommissionsByDays($transactions, $filters);
                }
                if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumeCommissionsByTwentyDays($transactions, $filters);
                }
                if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumeCommissionsByFortyDays($transactions, $filters);
                }
                if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumeCommissionsByWeeks($transactions, $filters);
                }
                if ($diffInDays > 140) {
                    return $this->getResumeCommissionsByMonths($transactions, $filters);
                }
            }
        });
    }

    public function getResumeCommissionsByHours($transactions, $filters)
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

        $resume = $transactions
            ->select(DB::raw("transactions.value as commission, HOUR(sales.start_date) as hour"))
            ->get();

        $comissionData = [];
        foreach ($labelList as $label) {
            $comissionValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $comissionValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round(
                ($comissionData[count($comissionData) - 1] / $comissionData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $comissionData,
            ],
            "total" => foxutils()->formatMoney($total / 100),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCommissionsByDays($transactions, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $labelList = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format("d/m"));
            $dataFormated = $dataFormated->addDays(1);
        }

        $resume = $transactions
            ->select(DB::raw("transactions.value as commission, DATE(sales.start_date) as date"))
            ->get();

        $comissionData = [];
        foreach ($labelList as $label) {
            $comissionValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("d/m") == $label) {
                    $comissionValue += foxutils()->onlyNumbers($r->commission);
                }
            }
            array_push($comissionData, $comissionValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round(
                ($comissionData[count($comissionData) - 1] / $comissionData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $comissionData,
            ],
            "total" => foxutils()->formatMoney($total / 100),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCommissionsByTwentyDays($transactions, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
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

        $total = $transactions->sum("value");
        $resume = $transactions
            ->select(DB::raw("transactions.value as commission, DATE(sales.start_date) as date"))
            ->get();

        $comissionData = [];
        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                if (
                    Carbon::parse($r->date)
                        ->subDays(1)
                        ->format("d/m") == $label ||
                    Carbon::parse($r->date)->format("d/m") == $label
                ) {
                    $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round(
                ($comissionData[count($comissionData) - 1] / $comissionData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $comissionData,
            ],
            "total" => foxutils()->formatMoney($total / 100),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCommissionsByFortyDays($transactions, $filters)
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

        $total = $transactions->sum("value");
        $resume = $transactions
            ->select(DB::raw("transactions.value as commission, DATE(sales.start_date) as date"))
            ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if (
                        Carbon::parse($r->date)
                            ->addDays($x)
                            ->format("d/m") == $label
                    ) {
                        $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                    }
                }
            }
            array_push($comissionData, $comissionDataValue);
        }

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round(
                ($comissionData[count($comissionData) - 1] / $comissionData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $comissionData,
            ],
            "total" => foxutils()->formatMoney($total / 100),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCommissionsByWeeks($transactions, $filters)
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

        $total = $transactions->sum("value");
        $resume = $transactions
            ->select(DB::raw("transactions.value as commission, DATE(sales.start_date) as date"))
            ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if (
                        Carbon::parse($r->date)
                            ->addDays($x)
                            ->format("d/m") == $label
                    ) {
                        $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                    }
                }
            }
            array_push($comissionData, $comissionDataValue);
        }

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round(
                ($comissionData[count($comissionData) - 1] / $comissionData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $comissionData,
            ],
            "total" => foxutils()->formatMoney($total / 100),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCommissionsByMonths($transactions, $filters)
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

        $total = $transactions->sum("value");
        $resume = $transactions
            ->select(DB::raw("transactions.value as commission, DATE(sales.start_date) as date"))
            ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;
            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("m/y") == $label) {
                    $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }
            array_push($comissionData, $comissionDataValue);
        }

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round(
                ($comissionData[count($comissionData) - 1] / $comissionData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $comissionData,
            ],
            "total" => foxutils()->formatMoney($total / 100),
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumePendings($filters)
    {
        $user = Auth::user();
        $filters['company_id'] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = 'pendings-resume-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters,$ownerId) {
            $showFromApi = str_starts_with($filters['project_id'],'TOKEN');
            $projectId = hashids_decode(str_replace('TOKEN-','',$filters['project_id']));
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $date["startDate"] = $dateRange[0];
            $date["endDate"] = $dateRange[1];

            $transactions = Transaction::where('status_enum', Transaction::STATUS_PAID)
                            ->where('user_id', $ownerId)
                            ->where('company_id', $filters['company_id'])
                            ->join('sales', 'sales.id', 'transactions.sale_id')
                            ->whereBetween('sales.start_date', [ $dateRange[0].' 00:00:00', $dateRange[1]. ' 23:59:59' ])
                            ->where('sales.api_flag', $showFromApi)
                            ->where($showFromApi ? 'sales.api_token_id' : 'sales.project_id', $projectId);

            if ($transactions->count() == 0) {
                return null;
            }

            if ($date["startDate"] == $date["endDate"]) {
                return $this->getResumePendingsByHours($transactions, $filters);
            }
            if ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumePendingsByDays($transactions, $filters);
                }
                if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumePendingsByTwentyDays($transactions, $filters);
                }
                if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumePendingsByFortyDays($transactions, $filters);
                }
                if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumePendingsByWeeks($transactions, $filters);
                }
                if ($diffInDays > 140) {
                    return $this->getResumePendingsByMonths($transactions, $filters);
                }
            }
        });
    }

    public function getResumePendingsByHours($transactions, $filters)
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

        $resume = $transactions->select(DB::raw("transactions.value, HOUR(sales.start_date) as hour"))->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }
            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ",", ".");

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
            "total" => $total,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumePendingsByDays($transactions, $filters)
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

        $resume = $transactions->select(DB::raw("transactions.value"))->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("d-m") == $label) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }
            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ",", ".");

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
            "total" => $total,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumePendingsByTwentyDays($transactions, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $transactions->select(DB::raw("DATE(start_date) as date, transactions.value"))->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (
                    Carbon::parse($r->date)
                        ->subDays(1)
                        ->format("d/m") == $label ||
                    Carbon::parse($r->date)->format("d/m") == $label
                ) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }
            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ",", ".");

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
            "total" => $total,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumePendingsByFortyDays($transactions, $filters)
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $transactions->select(DB::raw("DATE(start_date) as date, transactions.value"))->get();

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
                        $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                    }
                }
            }
            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ",", ".");

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
            "total" => $total,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumePendingsByWeeks($transactions, $filters)
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $transactions->select(DB::raw("DATE(start_date) as date, transactions.value"))->get();

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
                        $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                    }
                }
            }
            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ",", ".");

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
            "total" => $total,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumePendingsByMonths($transactions, $filters)
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $transactions->select(DB::raw("DATE(start_date) as date, transactions.value"))->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("m/y") == $label) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }
            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ",", ".");

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
            "total" => $total,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCashbacks($filters)
    {
        //o cache deve conter sempre o hash da empresa
        if(empty($filters['company_id'])){
            $filters['company_id'] = hashids_encode(auth()->user()->company_default);
        }

        $cacheName = 'cashback-resume-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters) {
            $showFromApi = str_starts_with($filters['project_id'],'TOKEN');
            $projectId = hashids_decode(str_replace('TOKEN-','',$filters['project_id']));
            $companyId = hashids_decode($filters['company_id']);
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);

            $cashbacks = Cashback::with('sale')
                        ->join('sales', 'sales.id', 'cashbacks.sale_id')
                        ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                        ->where('company_id', $companyId)
                        ->where('sales.api_flag', $showFromApi)
                        ->where($showFromApi ? 'sales.api_token_id' : 'sales.project_id', $projectId);

            $date["startDate"] = $dateRange[0];
            $date["endDate"] = $dateRange[1];

            $countCashbacks = $cashbacks->count();

            if ($date["startDate"] == $date["endDate"]) {
                return $this->getResumeCashbacksByHours($cashbacks, $countCashbacks, $filters);
            }

            if ($date['startDate'] != $date['endDate']) {
                $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                $diffInDays = $endDate->diffInDays($startDate);

                if ($diffInDays <= 20) {
                    return $this->getResumeCashbacksByDays($cashbacks, $countCashbacks, $filters);
                }
                if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getResumeCashbacksByTwentyDays($cashbacks, $countCashbacks, $filters);
                }
                if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getResumeCashbacksByFortyDays($cashbacks, $countCashbacks, $filters);
                }
                if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getResumeCashbacksByWeeks($cashbacks, $countCashbacks, $filters);
                }
                if ($diffInDays > 140) {
                    return $this->getResumeCashbacksByMonths($cashbacks, $countCashbacks, $filters);
                }
            }
        });
    }

    public function getResumeCashbacksByHours($cashbacks, $countCashbacks, $filters)
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

        $cashbackData = [];

        $resume = $cashbacks->select(DB::raw("cashbacks.value as cashback, HOUR(sales.start_date) as hour"))->get();

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }
            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ",", ".");

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round(
                ($cashbackData[count($cashbackData) - 1] / $cashbackData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $cashbackData,
            ],
            "total" => $total,
            "count" => $countCashbacks,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCashbacksByDays($cashbacks, $countCashbacks, $filters)
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

        $resume = $cashbacks->select(DB::raw("cashbacks.value as cashback, DATE(sales.start_date) as date"))->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("d-m") == $label) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }
            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ",", ".");

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round(
                ($cashbackData[count($cashbackData) - 1] / $cashbackData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $cashbackData,
            ],
            "total" => $total,
            "count" => $countCashbacks,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCashbacksByTwentyDays($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set("America/Sao_Paulo");

        $labelList = [];

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $cashbacks->select(DB::raw("cashbacks.value as cashback, DATE(sales.start_date) as date"))->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (
                    Carbon::parse($r->date)
                        ->subDays(1)
                        ->format("d/m") == $label ||
                    Carbon::parse($r->date)->format("d/m") == $label
                ) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ",", ".");

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round(
                ($cashbackData[count($cashbackData) - 1] / $cashbackData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $cashbackData,
            ],
            "total" => $total,
            "count" => $countCashbacks,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCashbacksByFortyDays($cashbacks, $countCashbacks, $filters)
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $cashbacks->select(DB::raw("cashbacks.value as cashback, DATE(sales.start_date) as date"))->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if (
                        Carbon::parse($r->date)
                            ->addDays($x)
                            ->format("d/m") == $label
                    ) {
                        $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                    }
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ",", ".");

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round(
                ($cashbackData[count($cashbackData) - 1] / $cashbackData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $cashbackData,
            ],
            "total" => $total,
            "count" => $countCashbacks,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCashbacksByWeeks($cashbacks, $countCashbacks, $filters)
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $cashbacks->select(DB::raw("cashbacks.value as cashback, DATE(sales.start_date) as date"))->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if (
                        Carbon::parse($r->date)
                            ->addDays($x)
                            ->format("d/m") == $label
                    ) {
                        $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                    }
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ",", ".");

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round(
                ($cashbackData[count($cashbackData) - 1] / $cashbackData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $cashbackData,
            ],
            "total" => $total,
            "count" => $countCashbacks,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getResumeCashbacksByMonths($cashbacks, $countCashbacks, $filters)
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

        $dateRange[1] = date("Y-m-d", strtotime($dateRange[1] . " + 1 day"));

        $resume = $cashbacks->select(DB::raw("cashbacks.value as cashback, DATE(sales.start_date) as date"))->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format("m/y") == $label) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ",", ".");

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round(
                ($cashbackData[count($cashbackData) - 1] / $cashbackData[0] - 1) * 100,
                0,
                PHP_ROUND_HALF_UP
            );
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
                "values" => $cashbackData,
            ],
            "total" => $total,
            "count" => $countCashbacks,
            // 'variation' => [
            //     'value' => $variation.'%',
            //     'color' => $color
            // ]
        ];
    }

    public function getFinancesResume($filters)
    {
        $user = Auth::user();
        $filters['company_id'] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = 'finances-balances-resume-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters,$ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters['project_id'],'TOKEN');
            $projectId = hashids_decode(str_replace('TOKEN-','',$filters['project_id']));

            $transactions = Transaction::where('user_id', $ownerId)
                            ->join('sales', 'sales.id', 'transactions.sale_id')
                            ->where('company_id',$filters['company_id'])
                            ->whereBetween('start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
                            ->whereIn('status_enum', [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ])
                            ->whereNull('invitation_id')
                            ->where('sales.api_flag', $showFromApi)
                            ->where($showFromApi ? 'sales.api_token_id' : 'sales.project_id', $projectId);

            $queryCount = $transactions->count();

            $queryAverageTicket = $transactions->avg("transactions.value");

            $queryComission = $transactions->sum("transactions.value");

            $queryChargeback = Transaction::where('user_id', $ownerId)
                                ->join('sales', 'sales.id', 'transactions.sale_id')
                                ->where('company_id',$filters['company_id'])
                                ->where('sales.status', Sale::STATUS_CHARGEBACK)
                                ->whereBetween('sales.start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
                                ->whereNull('invitation_id')
                                ->where('status_enum', Transaction::STATUS_CHARGEBACK)
                                ->where('sales.api_flag', $showFromApi)
                                ->where($showFromApi ? 'sales.api_token_id' : 'sales.project_id', $projectId)
                                ->sum('transactions.value');

            return [
                "transactions" => $queryCount,
                "average_ticket" => foxutils()->formatMoney($queryAverageTicket / 100),
                "comission" => foxutils()->formatMoney($queryComission / 100),
                "chargeback" => foxutils()->formatMoney($queryChargeback / 100),
            ];
        });
    }

    public function getFinancesCashbacks($filters)
    {
        $user = Auth::user();
        $filters['company_id'] = $user->company_default;
        $ownerId = $user->getAccountOwnerId();

        $cacheName = 'cashback-data-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters,$ownerId) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $showFromApi = str_starts_with($filters['project_id'],'TOKEN');
            $projectId = hashids_decode(str_replace('TOKEN-','',$filters['project_id']));

            $cashbacks =    Cashback::join('sales','cashbacks.sale_id','=','sales.id')
                            ->where('cashbacks.user_id',$ownerId)
                            ->where('cashbacks.company_id',$filters['company_id'])
                            ->whereBetween('cashbacks.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->where('sales.api_flag', $showFromApi)
                            ->where($showFromApi ? 'sales.api_token_id' : 'sales.project_id', $projectId);

            $cashbacksValue = $cashbacks->sum("value");
            $cashbacksCount = $cashbacks->count();

            return [
                "value" => foxutils()->formatMoney($cashbacksValue / 100),
                "quantity" => $cashbacksCount,
            ];
        });
    }

    public function getFinancesPendings()
    {
        $user = Auth::user();

        $cacheName = 'pending-data-'.$user->company_default;
        return cache()->remember($cacheName, 300, function() use($user) {
            $company = Company::find($user->company_default);

            $companyService = new CompanyBalanceService($company);
            $balancesResume = $companyService->getResumes();

            $pendingBalance = array_sum(array_column($balancesResume, "pending_balance"));
            $pendingBalanceCount = array_sum(array_column($balancesResume, "pending_balance_count"));

            return [
                "value" => foxutils()->formatMoney($pendingBalance / 100),
                "amount" => $pendingBalanceCount,
            ];
        });
    }

    public function getFinancesBlockeds()
    {
        $user = Auth::user();

        $cacheName = 'blocked-data-'.$user->company_default;
        return cache()->remember($cacheName, 300, function() use($user) {

            $company = Company::find($user->company_default);

            $companyService = new CompanyBalanceService($company);
            $balancesResume = $companyService->getResumes();

            $blockedBalance = array_sum(array_column($balancesResume, "blocked_balance"));
            $blockedBalanceCount = array_sum(array_column($balancesResume, "blocked_balance_count"));

            if ($blockedBalance == 0 && $blockedBalanceCount == 0) {
                return null;
            }

            return [
                "value" => foxutils()->formatMoney($blockedBalance / 100),
                "amount" => $blockedBalanceCount,
            ];
        });
    }

    public function getFinancesDistribuitions()
    {
        $user = Auth::user();

        $cacheName = 'distribuitions-data-'.$user->company_default;
        return cache()->remember($cacheName, 300, function() use($user)
        {
            $company = Company::find($user->company_default);

            $companyService = new CompanyBalanceService($company);
            $balancesResume = $companyService->getResumes();

            $availableBalance = array_sum(array_column($balancesResume, "available_balance"));
            $pendingBalance = array_sum(array_column($balancesResume, "pending_balance"));
            $blockedBalance = array_sum(array_column($balancesResume, "blocked_balance"));
            $totalBalance = array_sum(array_column($balancesResume, "total_balance"));

            return [
                "available" => [
                    "value" => foxutils()->formatMoney($availableBalance / 100),
                    "percentage" => !empty($totalBalance)
                        ? round(($availableBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP)
                        : 0,
                    "color" => "green",
                ],
                "pending" => [
                    "value" => foxutils()->formatMoney($pendingBalance / 100),
                    "percentage" => !empty($totalBalance)
                        ? round(($pendingBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP)
                        : 0,
                    "color" => "yellow",
                ],
                "blocked" => [
                    "value" => foxutils()->formatMoney(($blockedBalance) / 100),
                    "percentage" => !empty($totalBalance)
                        ? round(($blockedBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP)
                        : 0,
                    "color" => "red",
                ],
                "total" => $totalBalance > 0 ? foxutils()->formatMoney($totalBalance / 100) : null,
            ];
        });
    }

    public function getFinancesWithdrawals()
    {
        $user = Auth::user();

        $cacheName = 'withdrawals-data-'.$user->company_default;
        return cache()->remember($cacheName, 300, function() use($user) {
            date_default_timezone_set('America/Sao_Paulo');

            $dateEnd = date('Y-m-d');
            $dateStart = date('Y-m-d', strtotime($dateEnd . ' -5 month'));

            $withdrawals =  Withdrawal::where('company_id', $user->company_default)
                            ->whereBetween('release_date', [ $dateStart.' 00:00:00', $dateEnd.' 23:59:59' ])
                            ->where('status', Withdrawal::STATUS_TRANSFERRED);

            $transactions = Transaction::where('transactions.company_id', $user->company_default)
                            ->where('user_id', $user->getAccountOwnerId())
                            ->join('sales', 'transactions.sale_id', 'sales.id')
                            ->whereIn('status_enum', [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ])
                            ->whereBetween('sales.start_date', [ $dateStart.' 00:00:00', $dateEnd.' 23:59:59' ]);

            $dateStart = Carbon::parse($dateStart);
            $dateEnd = Carbon::parse($dateEnd);

            $portugueseMonths = [
                "Jan" => "Jan",
                "Feb" => "Fev",
                "Mar" => "Mar",
                "Apr" => "Abr",
                "May" => "Mai",
                "Jun" => "Jun",
                "Jul" => "Jul",
                "Aug" => "Ago",
                "Sep" => "Set",
                "Oct" => "Out",
                "Nov" => "Nov",
                "Dec" => "Dez",
            ];

            $labelList = [];
            $portugueseLabelList = [];
            while ($dateStart->lessThanOrEqualTo($dateEnd)) {
                array_push($labelList, $dateStart->format("M"));
                array_push($portugueseLabelList, $portugueseMonths[$dateStart->format("M")]);
                $dateStart = $dateStart->addMonths(1);
            }

            $withdrawals = $withdrawals->select(DB::raw("value, DATE(release_date) as date"))->get();

            $transactions = $transactions->select(DB::raw("transactions.value, DATE(sales.start_date) as date"))->get();

            if (count($withdrawals) == 0 && count($transactions) == 0) {
                return null;
            }

            $withdrawalData = [];
            $transactionData = [];

            $labelList = array_reverse($labelList);
            foreach ($labelList as $label) {
                $withdrawalDataValue = 0;
                $transactionDataValue = 0;

                foreach ($withdrawals as $withdrawal) {
                    if (Carbon::parse($withdrawal->date)->format("M") == $label) {
                        $withdrawalDataValue += intval(foxutils()->onlyNumbers($withdrawal->value));
                    }
                }

                foreach ($transactions as $transaction) {
                    if (Carbon::parse($transaction->date)->format("M") == $label) {
                        $transactionDataValue += intval(foxutils()->onlyNumbers($transaction->value));
                    }
                }

                array_push($withdrawalData, $withdrawalDataValue);
                array_push($transactionData, $transactionDataValue);
            }

            $totalWithdrawal = array_sum($withdrawalData);
            $totalTransactions = array_sum($transactionData);

            return [
                "chart" => [
                    "labels" => array_reverse($portugueseLabelList),
                    "withdrawal" => [
                        "values" => $withdrawalData,
                        "total" => foxutils()->formatMoney($totalWithdrawal / 100),
                    ],
                    "income" => [
                        "values" => $transactionData,
                        "total" => foxutils()->formatMoney($totalTransactions / 100),
                    ],
                ],
            ];
        });
    }
}
