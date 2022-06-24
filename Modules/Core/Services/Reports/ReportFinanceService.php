<?php

namespace Modules\Core\Services\Reports;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\Safe2PayService;

class ReportFinanceService
{
    public function getResumeCommissions($filters)
    {
        try {
            $cacheName = 'comissions-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $projectId = hashids_decode($filters['project_id']);

                $transactions = Transaction::join('sales', 'sales.id', 'transactions.sale_id')
                                            ->where('sales.project_id', $projectId)
                                            ->whereBetween('sales.start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
                                            ->whereNull('transactions.invitation_id')
                                            ->whereIn('transactions.status_enum', [ Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID ]);

                $date['startDate'] = $dateRange[0];
                $date['endDate'] = $dateRange[1];

                if ($date['startDate'] == $date['endDate']) {
                    return $this->getResumeCommissionsByHours($transactions, $filters);
                } elseif ($date['startDate'] != $date['endDate']) {
                    $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                    $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                    $diffInDays = $endDate->diffInDays($startDate);

                    if ($diffInDays <= 20) {
                        return $this->getResumeCommissionsByDays($transactions, $filters);
                    } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                        return $this->getResumeCommissionsByTwentyDays($transactions, $filters);
                    } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                        return $this->getResumeCommissionsByFortyDays($transactions, $filters);
                    } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                        return $this->getResumeCommissionsByWeeks($transactions, $filters);
                    } elseif ($diffInDays > 140) {
                        return $this->getResumeCommissionsByMonths($transactions, $filters);
                    }
                }
            });
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeCommissionsByHours($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
            $labelList   = [];
            $currentHour = date('H');
            $startHour   = 0;

            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . 'h');
                $startHour++;
            }
        } else {
            $labelList = [
                '0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h',
                '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h',
            ];
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value as commission, HOUR(sales.start_date) as hour'))
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
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => foxutils()->formatMoney($total / 100),
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $comissionValue += foxutils()->onlyNumbers($r->commission);
                }
            }

            array_push($comissionData, $comissionValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => foxutils()->formatMoney($total / 100),
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByTwentyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(2);
            if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => foxutils()->formatMoney($total / 100),
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByFortyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(3);
            if ($dataFormated->diffInDays($endDate) < 3) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                    }
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => foxutils()->formatMoney($total / 100),
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByWeeks($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(7);
            if ($dataFormated->diffInDays($endDate) < 7) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
        ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                    }
                }
            }

            array_push($comissionData, $comissionDataValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => foxutils()->formatMoney($total / 100),
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCommissionsByMonths($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $resume = $transactions->select(DB::raw('transactions.value as commission, DATE(sales.start_date) as date'))
                                ->get();

        $comissionData = [];

        foreach ($labelList as $label) {
            $comissionDataValue = 0;
            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $comissionDataValue += intval(preg_replace("/[^0-9]/", "", $r->commission));
                }
            }
            array_push($comissionData, $comissionDataValue);
        }

        $total = array_sum($comissionData);

        $variation = 0;
        if ($comissionData[0] > 0) {
            $variation = round((($comissionData[count($comissionData) - 1] / $comissionData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $comissionData
            ],
            'total' => foxutils()->formatMoney($total / 100),
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendings($filters)
    {
        try {
            $cacheName = 'pendings-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $projectId = hashids_decode($filters['project_id']);
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $date['startDate'] = $dateRange[0];
                $date['endDate'] = $dateRange[1];

                $transactions = Transaction::where('status_enum', Transaction::STATUS_PAID)
                                            ->join('sales', 'sales.id', 'transactions.sale_id')
                                            ->whereBetween('sales.start_date', [ $dateRange[0].' 00:00:00', $dateRange[1]. ' 23:59:59' ])
                                            ->where('sales.project_id', $projectId);

                if ($date['startDate'] == $date['endDate']) {
                    return $this->getResumePendingsByHours($transactions, $filters);
                } elseif ($date['startDate'] != $date['endDate']) {
                    $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                    $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                    $diffInDays = $endDate->diffInDays($startDate);

                    if ($diffInDays <= 20) {
                        return $this->getResumePendingsByDays($transactions, $filters);
                    } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                        return $this->getResumePendingsByTwentyDays($transactions, $filters);
                    } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                        return $this->getResumePendingsByFortyDays($transactions, $filters);
                    } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                        return $this->getResumePendingsByWeeks($transactions, $filters);
                    } elseif ($diffInDays > 140) {
                        return $this->getResumePendingsByMonths($transactions, $filters);
                    }
                }
            });
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumePendingsByHours($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
            $labelList   = [];
            $currentHour = date('H');
            $startHour   = 0;

            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . 'h');
                $startHour++;
            }
        } else {
            $labelList = [
                '0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h',
                '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h',
            ];
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value'))
        ->get();

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

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $resume = $transactions
        ->select(DB::raw('transactions.value'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByTwentyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(2);
            if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->select(DB::raw('DATE(start_date) as date, transactions.value'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByFortyDays($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(3);
            if ($dataFormated->diffInDays($endDate) < 3) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->select(DB::raw('DATE(start_date) as date, transactions.value'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByWeeks($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(7);
            if ($dataFormated->diffInDays($endDate) < 7) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->select(DB::raw('DATE(start_date) as date, transactions.value'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumePendingsByMonths($transactions, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $transactions
        ->select(DB::raw('DATE(start_date) as date, transactions.value'))
        ->get();

        $saleData = [];

        foreach ($labelList as $label) {
            $saleDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $saleDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = number_format(array_sum($saleData) / 100, 2, ',', '.');

        $variation = 0;
        if ($saleData[0] > 0) {
            $variation = round((($saleData[count($saleData) - 1] / $saleData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $saleData
            ],
            'total' => $total,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacks($filters)
    {
        try {
            $cacheName = 'cashback-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $projectId = hashids_decode($filters['project_id']);
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);

                $cashbacks = Cashback::with('sale')
                                        ->join('sales', 'sales.id', 'cashbacks.sale_id')
                                        ->whereBetween('start_date', [ $dateRange[0], $dateRange[1] ])
                                        ->where('sales.project_id', $projectId);

                $date['startDate'] = $dateRange[0];
                $date['endDate'] = $dateRange[1];

                $countCashbacks = $cashbacks->count();

                if ($date['startDate'] == $date['endDate']) {
                    return $this->getResumeCashbacksByHours($cashbacks, $countCashbacks, $filters);
                } elseif ($date['startDate'] != $date['endDate']) {
                    $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
                    $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
                    $diffInDays = $endDate->diffInDays($startDate);

                    if ($diffInDays <= 20) {
                        return $this->getResumeCashbacksByDays($cashbacks, $countCashbacks, $filters);
                    } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                        return $this->getResumeCashbacksByTwentyDays($cashbacks, $countCashbacks, $filters);
                    } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                        return $this->getResumeCashbacksByFortyDays($cashbacks, $countCashbacks, $filters);
                    } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                        return $this->getResumeCashbacksByWeeks($cashbacks, $countCashbacks, $filters);
                    } elseif ($diffInDays > 140) {
                        return $this->getResumeCashbacksByMonths($cashbacks, $countCashbacks, $filters);
                    }
                }
            });
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeCashbacksByHours($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        if (Carbon::parse($dateRange[0])->format('m/d/y') == Carbon::now()->format('m/d/y')) {
            $labelList   = [];
            $currentHour = date('H');
            $startHour   = 0;

            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . 'h');
                $startHour++;
            }
        } else {
            $labelList = [
                '0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h',
                '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h',
            ];
        }

        $cashbackData = [];

        $resume = $cashbacks
        ->select(DB::raw('cashbacks.value as cashback, HOUR(sales.start_date) as hour'))
        ->get();

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if ($r->hour == preg_replace("/[^0-9]/", "", $label)) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'count' => $countCashbacks,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByDays($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $labelList    = [];
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d-m'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $resume = $cashbacks
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'count' => $countCashbacks,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByTwentyDays($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList    = [];

        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0])->addDays(1);
        $endDate      = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(2);
            if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->subDays(1)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'count' => $countCashbacks,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByFortyDays($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(2);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(3);
            if ($dataFormated->diffInDays($endDate) < 3) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                    }
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'count' => $countCashbacks,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByWeeks($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0])->addDays(6);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m'));
            $dataFormated = $dataFormated->addDays(7);
            if ($dataFormated->diffInDays($endDate) < 7) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                array_push($labelList, $dataFormated->format('d/m'));
                break;
            }
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                    }
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'count' => $countCashbacks,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    public function getResumeCashbacksByMonths($cashbacks, $countCashbacks, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('m/y'));
            $dataFormated = $dataFormated->addMonths(1);
        }

        $dateRange[1] = date('Y-m-d', strtotime($dateRange[1] . ' + 1 day'));

        $resume = $cashbacks
        ->select(DB::raw('cashbacks.value as cashback, DATE(sales.start_date) as date'))
        ->get();

        $cashbackData = [];

        foreach ($labelList as $label) {
            $cashbackDataValue = 0;

            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $cashbackDataValue += intval(preg_replace("/[^0-9]/", "", $r->cashback));
                }
            }

            array_push($cashbackData, $cashbackDataValue);
        }

        $total = number_format(array_sum($cashbackData) / 100, 2, ',', '.');

        $variation = 0;
        if ($cashbackData[0] > 0) {
            $variation = round((($cashbackData[count($cashbackData) - 1] / $cashbackData[0]) - 1) * 100, 0, PHP_ROUND_HALF_UP);
        }

        $color = 'grey';
        if ($variation > 0) {
            $color = 'green';
        } else if ($variation < 0) {
            $color = 'pink';
        }

        return [
            'chart' => [
                'labels' => $labelList,
                'values' => $cashbackData
            ],
            'total' => $total,
            'count' => $countCashbacks,
            'variation' => [
                'value' => $variation.'%',
                'color' => $color
            ]
        ];
    }

    function getFinancesResume($filters)
    {
        try {
            $cacheName = 'finances-balances-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $projectId = hashids_decode($filters['project_id']);

                $userId = auth()->user()->account_owner_id;

                $transactions = Transaction::where('user_id', $userId)
                                            ->join('sales', 'sales.id', 'transactions.sale_id')
                                            ->where('sales.project_id', $projectId)
                                            ->whereBetween('start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
                                            ->whereNull('invitation_id');

                $queryCount = $transactions->count();

                $queryAverageTicket = $transactions->avg('transactions.value');

                $queryComission = $transactions
                ->whereIn('status_enum', [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ])
                ->where('sales.project_id', $projectId)
                ->where('sales.status', Sale::STATUS_APPROVED)
                ->sum('transactions.value');

                $queryChargeback = Transaction::where('user_id', $userId)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->where('sales.project_id', $projectId)
                ->where('sales.status', Sale::STATUS_CHARGEBACK)
                ->whereBetween('sales.start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
                ->whereNull('invitation_id')
                ->where('status_enum', Transaction::STATUS_CHARGEBACK)
                ->sum('transactions.value');

                return [
                    'transactions' => $queryCount,
                    'average_ticket' => foxutils()->formatMoney($queryAverageTicket / 100),
                    'comission' => foxutils()->formatMoney($queryComission / 100),
                    'chargeback' => foxutils()->formatMoney($queryChargeback / 100)
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesCashbacks($filters)
    {
        try {
            $cacheName = 'cashback-data-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $userId = auth()->user()->account_owner_id;

                $cashbacks = Cashback::where('user_id', $userId)->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);

                $cashbacksValue = $cashbacks->sum('value');
                $cashbacksCount = $cashbacks->count();

                return [
                    'value' => foxutils()->formatMoney($cashbacksValue / 100),
                    'quantity' => $cashbacksCount
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesPendings()
    {
        try {
            $cacheName = 'pending-data-';
            return cache()->remember($cacheName, 300, function() {
                $defaultGateways = [
                    Safe2PayService::class,
                    AsaasService::class,
                    GetnetService::class,
                    GerencianetService::class,
                    CieloService::class,
                ];

                $balancesPendingValue = [];
                $balancesPendingCount = [];

                $companies = Company::where('user_id', auth()->user()->account_owner_id)->get();
                foreach($companies as $company) {
                    foreach($defaultGateways as $gatewayClass) {
                        $gateway = app()->make($gatewayClass);
                        $gateway->setCompany($company);

                        $balancesPendingValue[] = $gateway->getPendingBalance();
                        $balancesPendingCount[] = $gateway->getPendingBalanceCount();
                    }
                }

                $totalPendingValue = array_sum($balancesPendingValue);
                $totalPendingCount = array_sum($balancesPendingCount);

                return [
                    'value' => foxutils()->formatMoney($totalPendingValue / 100),
                    'amount' => $totalPendingCount
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesBlockeds()
    {
        try {
            $cacheName = 'blocked-data-';
            return cache()->remember($cacheName, 300, function() {
                $defaultGateways = [
                    Safe2PayService::class,
                    AsaasService::class,
                    GetnetService::class,
                    GerencianetService::class,
                    CieloService::class,
                ];

                $balancesBlockedValue = [];
                $balancesBlockedCount = [];

                $balancesBlockedPendingValue = [];
                $balancesBlockedPendinCount = [];

                $companies = Company::where('user_id', auth()->user()->account_owner_id)->get();
                foreach($companies as $company) {
                    foreach($defaultGateways as $gatewayClass) {
                        $gateway = app()->make($gatewayClass);
                        $gateway->setCompany($company);

                        $balancesBlockedValue[] = $gateway->getBlockedBalance();
                        $balancesBlockedCount[] = $gateway->getBlockedBalanceCount();

                        $balancesBlockedPendingValue[] = $gateway->getBlockedBalancePending();
                        $balancesBlockedPendinCount[] = $gateway->getBlockedBalancePendingCount();
                    }
                }

                $totalBlockedValue = array_sum($balancesBlockedValue) + array_sum($balancesBlockedPendingValue);
                $totalBlockedCount = array_sum($balancesBlockedCount) + array_sum($balancesBlockedPendinCount);

                return [
                    'value' => foxutils()->formatMoney($totalBlockedValue / 100),
                    'amount' => $totalBlockedCount
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    function getFinancesDistribuitions()
    {
        try {
            $cacheName = 'distribuitions-data-';
            return cache()->remember($cacheName, 300, function() {
                $defaultGateways = [
                    Safe2PayService::class,
                    AsaasService::class,
                    GetnetService::class,
                    GerencianetService::class,
                    CieloService::class,
                ];

                $balancesAvailable = [];
                $balancesPending = [];
                $balancesBlocked = [];
                $balancesBlockedPending = [];

                $companies = Company::where('user_id', auth()->user()->account_owner_id)->get();
                foreach($companies as $company) {
                    foreach($defaultGateways as $gatewayClass) {
                        $gateway = app()->make($gatewayClass);
                        $gateway->setCompany($company);

                        $balancesAvailable[] = $gateway->getAvailableBalance();
                        $balancesPending[] = $gateway->getPendingBalance();
                        $balancesBlocked[] = $gateway->getBlockedBalance();
                        $balancesBlockedPending[] = $gateway->getBlockedBalancePending();
                    }
                }

                $availableBalance = array_sum($balancesAvailable);
                $pendingBalance = array_sum($balancesPending);
                $blockedBalance = array_sum($balancesBlocked);
                $blockedBalancePending = array_sum($balancesBlockedPending);

                $totalBalance = ($availableBalance + $pendingBalance + $blockedBalance + $blockedBalancePending);

                return [
                    'available' => [
                        'value' => foxutils()->formatMoney($availableBalance / 100),
                        'percentage' => round(($availableBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP),
                        'color' => 'green'
                    ],
                    'pending' => [
                        'value' => foxutils()->formatMoney($pendingBalance / 100),
                        'percentage' => round(($pendingBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP),
                        'color' => 'yellow'
                    ],
                    'blocked' => [
                        'value' => foxutils()->formatMoney(($blockedBalance + $blockedBalancePending) / 100),
                        'percentage' => round((($blockedBalance + $blockedBalancePending) * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP),
                        'color' => 'red'
                    ],
                    'total' => foxutils()->formatMoney($totalBalance / 100),
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesWithdrawals()
    {
        try {
            $cacheName = 'withdrawals-data-';
            return cache()->remember($cacheName, 300, function() {
                date_default_timezone_set('America/Sao_Paulo');

                $dateEnd = date('Y-m-d');
                $dateStart = date('Y-m-d', strtotime($dateEnd . ' -5 month'));

                $companies = Company::where('user_id', auth()->user()->account_owner_id)->get()->pluck('id')->toArray();

                $withdrawals = Withdrawal::whereIn('company_id', $companies)->whereBetween('release_date', [ $dateStart.' 00:00:00', $dateEnd.' 23:59:59' ]);

                $transactions = Transaction::whereIn('transactions.company_id', $companies)
                ->join('sales', 'transactions.sale_id', 'sales.id')
                ->whereNotIn('sales.status', [
                    Sale::STATUS_CANCELED_ANTIFRAUD,
                    Sale::STATUS_REFUSED,
                    Sale::STATUS_SYSTEM_ERROR
                ])
                ->whereBetween('sales.start_date', [ $dateStart.' 00:00:00', $dateEnd.' 23:59:59' ]);

                $dateStart = Carbon::parse($dateStart);
                $dateEnd = Carbon::parse($dateEnd);

                $labelList = [];
                while ($dateStart->lessThanOrEqualTo($dateEnd)) {
                    array_push($labelList, $dateStart->format('M'));
                    $dateStart = $dateStart->addMonths(1);
                }

                $resumeWithdrawals = $withdrawals
                ->select(DB::raw('value, DATE(release_date) as date'))
                ->get();

                $resumeTransactions = $transactions
                ->select(DB::raw('transactions.value, DATE(sales.start_date) as date'))
                ->get();

                $withdrawalData = [];
                $transactionData = [];

                $labelList = array_reverse($labelList);
                foreach ($labelList as $label) {
                    $withdrawalDataValue = 0;
                    $transactionDataValue = 0;

                    foreach ($resumeWithdrawals as $r) {
                        if (Carbon::parse($r->date)->format('M') == $label) {
                            $withdrawalDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                        }
                    }

                    foreach ($resumeTransactions as $r) {
                        if (Carbon::parse($r->date)->format('M') == $label) {
                            $transactionDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                        }
                    }

                    array_push($withdrawalData, $withdrawalDataValue);
                    array_push($transactionData, $transactionDataValue);
                }

                $totalWithdrawal = array_sum($withdrawalData);
                $totalTransactions = array_sum($transactionData);

                return [
                    'chart' => [
                        'labels' => $labelList,
                        'withdrawal' => [
                            'values' => $withdrawalData,
                            'total' => foxutils()->formatMoney($totalWithdrawal / 100)
                        ],
                        'income' => [
                            'values' => $transactionData,
                            'total' => foxutils()->formatMoney($totalTransactions / 100)
                        ]
                    ]
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
