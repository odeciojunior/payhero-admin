<?php

namespace Modules\Core\Services\Reports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

class ReportSaleService
{
    public function getResumeSales($filters)
    {
       try {
            $cacheName = 'sales-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);

                $sales = Sale::where('project_id', current(Hashids::decode($filters['project_id'])))
                            ->where('owner_id', auth()->user()->id)
                            ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ]);

                if (!empty($filters['status'])) {
                    $salesModel = new Sale();
                    if ($filters['status'] === 'others') {
                        $statusNotIn = [
                            Sale::STATUS_APPROVED,
                            Sale::STATUS_PENDING,
                            Sale::STATUS_CANCELED,
                            Sale::STATUS_REFUSED,
                            Sale::STATUS_REFUNDED,
                            Sale::STATUS_CHARGEBACK
                        ];
                        $sales->whereNotIn('status', $statusNotIn);
                    } else {
                        $sales->where('status', $salesModel->present()->getStatus($filters['status']));
                    }
                }

                if ($dateRange['0'] == $dateRange['1']) {
                    return $this->getResumeSalesByHours($sales, $filters);
                } elseif ($dateRange['0'] != $dateRange['1']) {
                    $startDate  = Carbon::createFromFormat('Y-m-d', $dateRange['0'], 'America/Sao_Paulo');
                    $endDate    = Carbon::createFromFormat('Y-m-d', $dateRange['1'], 'America/Sao_Paulo');
                    $diffInDays = $endDate->diffInDays($startDate);

                    if ($diffInDays <= 20) {
                        return $this->getResumeSalesByDays($sales, $filters);
                    } elseif ($diffInDays > 20 && $diffInDays <= 40) {
                        return $this->getResumeSalesByTwentyDays($sales, $filters);
                    } elseif ($diffInDays > 40 && $diffInDays <= 60) {
                        return $this->getResumeSalesByFortyDays($sales, $filters);
                    } elseif ($diffInDays > 60 && $diffInDays <= 140) {
                        return $this->getResumeSalesByWeeks($sales, $filters);
                    } elseif ($diffInDays > 140) {
                        return $this->getResumeSalesByMonths($sales, $filters);
                    }
                }
            });
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeSalesByHours($sales, $filters)
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

        $resume = $sales->select(DB::raw('id as sale, HOUR(start_date) as hour'))->get();

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

        $total = array_sum($saleData);

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

    public function getResumeSalesByDays($sales, $filters)
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

        $resume = $sales
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('d-m') == $label) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

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

    public function getResumeSalesByTwentyDays($sales, $filters)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $labelList = [];
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);

        $dataFormated = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

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

        $resume = $sales
        ->select(DB::raw('id as sale, DATE(start_date) as date'))
        ->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if ((Carbon::parse($r->date)->format('d/m') == $label) || (Carbon::parse($r->date)->format('d/m') == $label)) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

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

    public function getResumeSalesByFortyDays($sales, $filters)
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

        $resume = $sales
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                for ($x = 1; $x <= 3; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $saleDataValue += 1;
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

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

    public function getResumeSalesByWeeks($sales, $filters)
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

        $resume = $sales
        ->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))
        ->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                for ($x = 1; $x <= 6; $x++) {
                    if ((Carbon::parse($r->date)->addDays($x)->format('d/m') == $label)) {
                        $saleDataValue += 1;
                    }
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

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

    public function getResumeSalesByMonths($sales, $filters)
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

        $resume = $sales->select(DB::raw('sales.id as sale, DATE(sales.start_date) as date'))->get();

        $saleData = [];
        foreach ($labelList as $label) {
            $saleDataValue = 0;
            foreach ($resume as $r) {
                if (Carbon::parse($r->date)->format('m/y') == $label) {
                    $saleDataValue += 1;
                }
            }

            array_push($saleData, $saleDataValue);
        }

        $total = array_sum($saleData);

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

    public function getResumeTypePayments($filters)
    {
        try {
            $cacheName = 'payment-type-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $projectId = hashids_decode($filters['project_id']);
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);

                $saleModel = new Sale();

                $query = $saleModel
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_APPROVED)
                ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                ->selectRaw('SUM(original_total_paid_value / 100) as total')
                ->selectRaw('SUM(IF(payment_method = 1, original_total_paid_value / 100, 0)) as total_credit_card')
                ->selectRaw('SUM(IF(payment_method = 2, original_total_paid_value / 100, 0)) as total_boleto')
                ->selectRaw('SUM(IF(payment_method = 4, original_total_paid_value / 100, 0)) as total_pix')
                ->first();

                $total = $query->total;

                $totalCreditCard = $query->total_credit_card;
                $percentageCreditCard = $totalCreditCard > 0 ? number_format(($totalCreditCard * 100) / $total, 2, '.', ',') : 0;

                $totalBoleto = $query->total_boleto;
                $percentageBoleto = $totalBoleto > 0 ? number_format(($totalBoleto * 100) / $total, 2, '.', ',') : 0;

                $totalPix = $query->total_pix;
                $percentagePix = $totalPix > 0 ? number_format(($totalPix * 100) / $total, 2, '.', ',') : 0;

                return [
                    'total' => number_format($total, 2, ',', '.'),
                    'credit_card' => [
                        'value' => number_format($totalCreditCard, 2, ',', '.'),
                        'percentage' => round($percentageCreditCard, 1, PHP_ROUND_HALF_UP).'%'
                    ],
                    'boleto' => [
                        'value' => number_format($totalBoleto, 2, ',', '.'),
                        'percentage' => round($percentageBoleto, 1, PHP_ROUND_HALF_UP).'%'
                    ],
                    'pix' => [
                        'value' => number_format($totalPix, 2, ',', '.'),
                        'percentage' => round($percentagePix, 1, PHP_ROUND_HALF_UP).'%'
                    ]
                ];
            });
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeProducts($filters)
    {
        try {
            $cacheName = 'products-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $projectId = hashids_decode($filters['project_id']);
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);

                $query = Product::join('products_plans_sales', 'products.id', 'products_plans_sales.product_id')
                ->join('sales', 'products_plans_sales.sale_id', 'sales.id')
                ->where('sales.status', Sale::STATUS_APPROVED)
                ->where('sales.project_id', $projectId)
                ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                ->select(DB::raw('products.name, products.photo as image, COUNT(*) as amount'))
                ->groupBy('products.id')
                ->orderByDesc('amount')
                ->limit(8)
                ->get();

                $total = 0;
                foreach($query as $r)
                {
                    $total += $r->amount;
                }

                $index = 0;
                foreach($query as $result)
                {
                    $percentage = round(number_format(($result->amount * 100) / $total, 2, '.', ','), 1, PHP_ROUND_HALF_UP);

                    $result->image = empty($result->image) ? 'https://cloudfox-files.s3.amazonaws.com/produto.svg' : $result->image;
                    $result->percentage = $percentage < 28 ? '28%' : $percentage.'%';
                    $result->color = $this->getColors($index);

                    $index++;
                }

                $productsArray = $query->toArray();

                return [
                    'products' => $productsArray,
                    'total' => $total
                ];
            });
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getSalesResume($filters)
    {
        try {
            $cacheName = 'sales-balance-resume-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $projectId = hashids_decode($filters['project_id']);

                $salesApproved = Sale::whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_APPROVED)
                ->count();

                $salesAverageTicket = Sale::whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_APPROVED)
                ->avg('original_total_paid_value');

                $salesComission = Transaction::join('sales', 'sales.id', 'transactions.sale_id')
                ->where('project_id', $projectId)
                ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                ->whereNull('invitation_id')
                ->whereIn('sales.status', [ 1, 2, 4, 7, 8, 12, 20, 21, 22 ])
                ->whereIn('status_enum', [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ])
                ->sum('transactions.value');

                $salesChargeback = Sale::whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_CHARGEBACK)
                ->sum('original_total_paid_value');

                return [
                    'transactions' => $salesApproved,
                    'average_ticket' => foxutils()->formatMoney($salesAverageTicket / 100),
                    'comission' => foxutils()->formatMoney($salesComission / 100),
                    'chargeback' => foxutils()->formatMoney($salesChargeback /100)
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSalesDistribuitions($filters)
    {
        try {
            $cacheName = 'sales-distribuition-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $projectId = hashids_decode($filters['project_id']);

                $salesApprovedSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_APPROVED)
                ->sum('original_total_paid_value');

                $salesPendingSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_PENDING)
                ->sum('original_total_paid_value');

                $salesCanceledSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_CANCELED)
                ->sum('original_total_paid_value');

                $salesRefusedSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_REFUSED)
                ->sum('original_total_paid_value');

                $salesRefundedSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_REFUNDED)
                ->sum('original_total_paid_value');

                $salesChargebackSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->where('status', Sale::STATUS_CHARGEBACK)
                ->sum('original_total_paid_value');

                $salesOtherSum = Sale::whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->where('project_id', $projectId)
                ->whereNotIn('status', [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_PENDING,
                    Sale::STATUS_CANCELED,
                    Sale::STATUS_REFUSED,
                    Sale::STATUS_REFUNDED
                ])
                ->sum('original_total_paid_value');

                $total = ($salesApprovedSum + $salesPendingSum + $salesCanceledSum + $salesRefusedSum + $salesRefundedSum + $salesChargebackSum + $salesOtherSum);

                return [
                    'total' => foxutils()->formatMoney($total / 100),
                    'approved' => [
                        'value' => foxutils()->formatMoney($salesApprovedSum / 100),
                        'percentage' => number_format(($salesApprovedSum * 100) / $total, 2, '.', ',')
                    ],
                    'pending' => [
                        'value' => foxutils()->formatMoney($salesPendingSum / 100),
                        'percentage' => number_format(($salesPendingSum * 100) / $total, 2, '.', ',')
                    ],
                    'canceled' => [
                        'value' => foxutils()->formatMoney($salesCanceledSum / 100),
                        'percentage' => number_format(($salesCanceledSum * 100) / $total, 2, '.', ',')
                    ],
                    'refused' => [
                        'value' => foxutils()->formatMoney($salesRefusedSum / 100),
                        'percentage' => number_format(($salesRefusedSum * 100) / $total, 2, '.', ',')
                    ],
                    'refunded' => [
                        'value' => foxutils()->formatMoney($salesRefundedSum / 100),
                        'percentage' => number_format(($salesRefundedSum * 100) / $total, 2, '.', ',')
                    ],
                    'chargeback' => [
                        'value' => foxutils()->formatMoney($salesChargebackSum / 100),
                        'percentage' => number_format(($salesChargebackSum * 100) / $total, 2, '.', ',')
                    ],
                    'other' => [
                        'value' => foxutils()->formatMoney($salesOtherSum / 100),
                        'percentage' => number_format(($salesOtherSum * 100) / $total, 2, '.', ',')
                    ]
                ];
            });
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAbandonedCarts($filters)
    {
        $cacheName = 'abandoned-carts-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters['project_id']);

            $checkoutsData = Checkout::select([
                DB::raw('SUM(CASE WHEN checkouts.status_enum = 2 THEN 1 ELSE 0 END) AS abandoned'),
                DB::raw('SUM(CASE WHEN checkouts.status_enum = 3 THEN 1 ELSE 0 END) AS recovered'),
            ])
            ->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
            ->where('project_id', $projectId)
            ->first();

            $recoveredValue = Sale::join('checkouts as checkout', function ($join) {
                                        $join->on('sales.checkout_id', '=', 'checkout.id');
                                        $join->where('checkout.status_enum', Checkout::STATUS_RECOVERED);
                                    })
                                    ->join('transactions as transaction', function ($join) {
                                        $join->on('sales.id', '=', 'transaction.sale_id');
                                        $join->where('checkout.status_enum', Checkout::STATUS_RECOVERED);
                                    })
                                    ->sum('transaction.value');

            return [
                'percentage' => $checkoutsData->abandoned > 0 ? number_format(($checkoutsData->recovered * 100) / $checkoutsData->abandoned, 1, '.', ',') . '%' : '0%',
                'value' => foxutils()->formatMoney($recoveredValue / 100)
            ];
        });
    }

    public function getOrderBump($filters)
    {
        $cacheName = 'order-bump-'.json_encode($filters);
        cache()->forget($cacheName);

        return cache()->remember($cacheName, 300, function() use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters['project_id']);

            $data = Transaction::select(DB::raw('count(*) as amount, sum(value) as value'))
                                ->join('sales', function($join) {
                                    $join->on('transactions.sale_id', 'sales.id');
                                })
                                ->whereBetween('transactions.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                                ->where('sales.has_order_bump', true)
                                ->where('sales.project_id', $projectId)
                                ->where('sales.status', Sale::STATUS_APPROVED)
                                ->where('user_id', auth()->user()->account_owner_id)
                                ->first();

            return [
                'value' => foxutils()->formatMoney($data->value / 100),
                'amount' => $data->amount
            ];
        });
    }

    public function getUpsell($filters)
    {
        $cacheName = 'upsell-'.json_encode($filters);
        cache()->forget($cacheName);
        return cache()->remember($cacheName, 300, function() use ($filters) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = hashids_decode($filters['project_id']);

            $data = Transaction::select(DB::raw('count(*) as amount, sum(value) as value'))
                                ->join('sales', function($join) {
                                    $join->on('transactions.sale_id', 'sales.id');
                                })
                                ->whereBetween('transactions.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                                ->whereNotNull('sales.upsell_id')
                                ->where('sales.project_id', $projectId)
                                ->where('sales.status', Sale::STATUS_APPROVED)
                                ->where('user_id', auth()->user()->account_owner_id)
                                ->first();

            return [
                'value' => foxutils()->formatMoney($data->value / 100),
                'amount' => $data->amount
            ];
        });
    }

    public function getConversion($filters)
    {
        try {
            $cacheName = 'conversion-'.json_encode($filters);
            return cache()->remember($cacheName, 300, function() use ($filters) {
                $dateRange = foxutils()->validateDateRange($filters["date_range"]);
                $projectId = hashids_decode($filters['project_id']);

                $query = Sale::where('project_id', $projectId)
                                ->whereBetween('start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                                ->selectRaw(DB::raw('SUM(CASE WHEN payment_method = 1 THEN 1 ELSE 0 END) AS total_credit_card'))
                                ->selectRaw(DB::raw('SUM(CASE WHEN payment_method = 1 and status = 1 THEN 1 ELSE 0 END) AS total_credit_card_approved'))
                                ->selectRaw(DB::raw('SUM(CASE WHEN payment_method = 2 THEN 1 ELSE 0 END) AS total_boleto'))
                                ->selectRaw(DB::raw('SUM(CASE WHEN payment_method = 2 and status = 1 THEN 1 ELSE 0 END) AS total_boleto_approved'))
                                ->selectRaw(DB::raw('SUM(CASE WHEN payment_method = 4 THEN 1 ELSE 0 END) AS total_pix'))
                                ->selectRaw(DB::raw('SUM(CASE WHEN payment_method = 4 and status = 1 THEN 1 ELSE 0 END) AS total_pix_approved'))
                                ->first();

                $totalCreditCard = $query->total_credit_card;
                $totalCreditCardApproved = $query->total_credit_card_approved;
                $percentageCreditCard = $totalCreditCard > 0 ? number_format(($totalCreditCardApproved * 100) / $totalCreditCard, 2, '.', ',') : 0;

                $totalBoleto = $query->total_boleto;
                $totalBoletoApproved = $query->total_boleto_approved;
                $percentageBoleto = $totalBoleto > 0 ? number_format(($totalBoletoApproved * 100) / $totalBoleto, 2, '.', ',') : 0;

                $totalPix = $query->total_pix;
                $totalPixApproved = $query->total_pix_approved;
                $percentagePix = $totalPix > 0 ? number_format(($totalPixApproved * 100) / $totalPix, 2, '.', ',') : 0;

                return [
                    'credit_card' => [
                        'total' => number_format($totalCreditCard, 0, '.', '.'),
                        'approved' => number_format($totalCreditCardApproved, 0, '.', '.'),
                        'percentage' => round($percentageCreditCard, 1, PHP_ROUND_HALF_UP).'%'
                    ],
                    'boleto' => [
                        'total' => number_format($totalBoleto, 0, '.', '.'),
                        'approved' => number_format($totalBoletoApproved, 0, '.', '.'),
                        'percentage' => round($percentageBoleto, 1, PHP_ROUND_HALF_UP).'%'
                    ],
                    'pix' => [
                        'total' => number_format($totalPix, 0, '.', '.'),
                        'approved' => number_format($totalPixApproved, 0, '.', '.'),
                        'percentage' => round($percentagePix, 1, PHP_ROUND_HALF_UP).'%'
                    ]
                ];
            });
        } catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao carregar dados.'], 400);
        }
    }

    public function getRecurrence($filters)
    {
        $cacheName = 'recurrency-'.json_encode($filters);
        return cache()->remember($cacheName, 300, function() use ($filters) {
            $projectId = hashids_decode($filters['project_id']);

            date_default_timezone_set('America/Sao_Paulo');
            config()->set('database.connections.mysql.strict', false);
            DB::reconnect();

            $sales = Sale::select([
                DB::raw('YEAR(sales.start_date) as year'),
                DB::raw('MONTH(sales.start_date) as month'),
                DB::raw('count(*) as amount'),
                DB::raw('(select count(*) from sales as s where s.customer_id = sales.customer_id limit 1) as sales_count')
            ])->where('sales.start_date', '>', now()->subMonths(6)->startOfMonth())
                ->where('sales.project_id', $projectId)
                ->having('sales_count', '>', 1)
                ->groupBy('year', 'month')
                ->get();

            config()->set('database.connections.mysql.strict', true);
            DB::reconnect();

            $labels = [];
            $values = [];

            foreach($sales as $sale) {
                array_push($labels, date('M', mktime(0, 0, 0, $sale->month, 0, 0)));
                array_push($values, $sale->sales_count);
            }

            $total = array_sum($values);

            return [
                'chart' => [
                    'labels' => $labels,
                    'values' => $values
                ],
                'total' => $total
            ];
        });
    }

    public function getColors($index = null, $hex = false)
    {
        $colors = [ 'blue', 'purple', 'pink', 'orange', 'yellow', 'light-blue', 'light-green', 'grey' ];

        if ($hex == true) {
            $colors = [ '#2E85EC', '#FF7900', '#665FE8', '#F43F5E' ];
        }

        if (!empty($index) || $index >= 0) {
            return $colors[$index];
        }

        return $colors;
    }
}
