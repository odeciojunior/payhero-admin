<?php

namespace Modules\Core\Services;


use Exception;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GetnetChargeback;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class ChargebackService
{

    function getQuery($filters)
    {

        $getnetChargebacks = GetnetChargeback::with(
            [
                'sale',
                'user',
                'sale.customer',
                'sale.plansSales',
                'sale.plansSales.plan',
                'sale.plansSales.plan.project',
                'company',
            ]
        );

        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $getnetChargebacks->whereBetween(
            $filters['date_type'],
            [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
        );

        if (!empty($filters['transaction'])) {
            preg_match_all('/[0-9A-Za-z]+/', $filters['transaction'], $matches);
            $transactions = array_map(
                function ($item) {
                    return is_numeric($item)
                        ? $item
                        : current(Hashids::connection('sale_id')->decode($item));
                },
                current($matches)
            );
            $getnetChargebacks->whereIn('sale_id', $transactions);
        }

        if (!empty($filters['fantasy_name'])) {
            $getnetChargebacks->whereHas(
                'company',
                function ($query) use ($filters) {
                    $query->where(
                        'fantasy_name',
                        'like',
                        '%' . $filters['fantasy_name'] . '%'
                    );
                }
            );
        }

        if (!empty($filters['project'])) {
            $projectId = current(Hashids::decode($filters['project']));
            $getnetChargebacks->where('project_id', $projectId);
        }

        if (!empty($filters['user'])) {
            $userId = current(Hashids::decode($filters['user']));
            $getnetChargebacks->where('user_id', $userId);
        }

        if (!empty($filters['customer'])) {

            $getnetChargebacks->whereHas(
                'sale',
                function ($query) use ($filters) {
                    $query->where(
                        'customer_id', $filters['customer']
                    );
                }
            );
        }

        if (!empty($filters['customer_document'])) {
            $document = $filters['customer_document'];
            $getnetChargebacks->whereHas(
                'sale.customer',
                function ($query) use ($document) {
                    $query->where('document', $document);
                }
            );
        }

        return $getnetChargebacks;

    }


    public function getTotalValueChargebacks($filters)
    {
        $getnetChargebacks = $this->getQuery($filters);

        return 'R$ ' . number_format(intval($getnetChargebacks->sum('amount')) / 100, 2, ',', '.');
    }

    public function getTotalChargebacks($filters)
    {
        $getnetChargebacks = $this->getQuery($filters);

        return $getnetChargebacks->count();
    }

    public function getTotalApprovedSales($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $totalSaleApproved = Sale::where('gateway_id', 15)
            ->where('payment_method', 1)
            ->whereIn('status', [1, 4, 7, 24]);

        if ($filters['date_type'] == 'transaction_date') {
            $totalSaleApproved->whereBetween(
                'start_date',
                [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
            );
        } else {
            $totalSaleApproved->whereHas('getnetChargebacks', function ($query) use ($dateRange) {
                $query->whereBetween(
                    'adjustment_date',
                    [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
                );
            });
        }

        if (!empty($filters['transaction'])) {
            preg_match_all('/[0-9A-Za-z]+/', $filters['transaction'], $matches);
            $transactions = array_map(
                function ($item) {
                    return is_numeric($item)
                        ? $item
                        : current(Hashids::connection('id')->decode($item));
                },
                current($matches)
            );
            $totalSaleApproved->whereIn('id', $transactions);
        }

        if (!empty($filters['fantasy_name'])) {
            $totalSaleApproved->whereHas(
                'user.companies',
                function ($query) use ($filters) {
                    $query->where(
                        'fantasy_name',
                        'like',
                        '%' . $filters['fantasy_name'] . '%'
                    );
                }
            );
        }

        if (!empty($filters['project'])) {
            $projectId = current(Hashids::decode($filters['project']));
            $totalSaleApproved->where('project_id', $projectId);
        }

        if (!empty($filters['user'])) {
            $userId = current(Hashids::decode($filters['user']));
            $totalSaleApproved->where('owner_id', $userId);
        }

        if (!empty($filters['customer'])) {
            $totalSaleApproved->where('customer_id', $filters['customer']);

        }

        if (!empty($filters['customer_document'])) {
            $document = $filters['customer_document'];
            $totalSaleApproved->whereHas(
                'customer',
                function ($query) use ($document) {
                    $query->where('document', $document);
                }
            );
        }

        return $totalSaleApproved->count();
    }

    public function getChargebackRateInPeriod(User $user, $startDate, $endDate): ?float
    {
        $getnetChargebacks = GetnetChargeback::with(
            [
                'sale',
                'user',
                'sale.customer',
                'sale.plansSales',
                'sale.plansSales.plan',
                'sale.plansSales.plan.project',
                'company',
            ]
        );

        $dateRange = FoxUtils::validateDateRange([$startDate, $endDate]);

        $getnetChargebacks->whereBetween(
            'sale.created_at',
            [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
        )->where('user_id', $user->id);

        $totalChargebacks = $getnetChargebacks->count();

        $approvedSales = Sale::where('gateway_id', 15)
            ->where('payment_method', Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn('status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])->whereBetween(
                'start_date',
                [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
            )->where('owner_id', $user->id);

        $totalApprovedSales = $approvedSales->count();

        $totalChargebackTax = $totalChargebacks > 0 ? ($totalChargebacks * 100 / $totalApprovedSales) : 0;
        return $totalChargebackTax;
    }

}
