<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;

class ReportSaleService
{
    public function getSalesResume($filters)
    {
        try {
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
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
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSalesDistribuitions($filters)
    {
        try {
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
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
                Sale::STATUS_REFUNDED,
                Sale::STATUS_CHARGEBACK
            ])
            ->sum('original_total_paid_value');

            $total = ($salesApprovedSum + $salesPendingSum + $salesCanceledSum + $salesRefusedSum + $salesRefundedSum + $salesChargebackSum + $salesOtherSum);

            return [
                'total' => foxutils()->formatMoney($total / 100),
                'approved' => [
                    'value' => foxutils()->formatMoney($salesApprovedSum / 100),
                    'percentage' => round(number_format(($salesApprovedSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ],
                'pending' => [
                    'value' => foxutils()->formatMoney($salesPendingSum / 100),
                    'percentage' => round(number_format(($salesPendingSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ],
                'canceled' => [
                    'value' => foxutils()->formatMoney($salesCanceledSum / 100),
                    'percentage' => round(number_format(($salesCanceledSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ],
                'refused' => [
                    'value' => foxutils()->formatMoney($salesRefusedSum / 100),
                    'percentage' => round(number_format(($salesRefusedSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ],
                'refunded' => [
                    'value' => foxutils()->formatMoney($salesRefundedSum / 100),
                    'percentage' => round(number_format(($salesRefundedSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ],
                'chargeback' => [
                    'value' => foxutils()->formatMoney($salesChargebackSum / 100),
                    'percentage' => round(number_format(($salesChargebackSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ],
                'other' => [
                    'value' => foxutils()->formatMoney($salesOtherSum / 100),
                    'percentage' => round(number_format(($salesOtherSum * 100) / $total, 2, '.', ','), 0, PHP_ROUND_HALF_UP)
                ]
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAbandonedCarts($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
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
    }

    public function getOrderBump($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $data = Transaction::select(DB::raw('count(*) as amount, sum(value) as value'))
                            ->join('sales', function($join) {
                                $join->on('transactions.sale_id', 'sales.id');
                            })
                            ->whereBetween('transactions.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->where('sales.has_order_bump', true)
                            ->where('sales.project_id', $projectId)
                            ->first();

        return [
            'value' => $data->value,
            'amount' => $data->amount
        ];
    }

    public function getUpsell($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $data = Transaction::select(DB::raw('count(*) as amount, sum(value) as value'))
                            ->join('sales', function($join) {
                                $join->on('transactions.sale_id', 'sales.id');
                            })
                            ->whereBetween('transactions.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->whereNotNull('sales.upsell_id')
                            ->where('sales.project_id', $projectId)
                            ->first();

        return [
            'value' => $data->value,
            'amount' => $data->amount
        ];
    }

    public function getConversion($filters)
    {
        try {
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
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

        } catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao carregar dados.'], 400);
        }
    }

    public function getRecurrence($filters)
    {
        return [];

        $projectId = hashids_decode($filters['project_id']);

        $sales = Customer::select(DB::raw("MONTH(customers.created_at) as month, count(*) as amount"))
        ->withCount(['sales' => function ($query) use($projectId) {
            $query->where('sales.start_date', '>', now()->subMonths(6)->startOfMonth())
                    ->where('sales.project_id', $projectId);
        }])
        ->having('sales_count', '>', 1)
        ->groupBy('month')
        ->get()
        ->toArray();

        return $sales;

    }
}
