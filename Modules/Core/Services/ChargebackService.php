<?php

namespace Modules\Core\Services;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\GetnetChargeback;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class ChargebackService
{

    function getQuery($filters)
    {

        $user = \Auth::user();
        $getnetChargebacks = GetnetChargeback::with(
            [
                'sale',
                'user',
                'sale.customer',
                'sale.plansSales',
                'sale.plansSales.plan',
                'sale.plansSales.plan.project',
                'sale.customer',
                'sale.contestations',
                'company',
            ]
        )->where('user_id', $user->account_owner_id);

        if(request()->has('from_contestation') || request('date_type') == 'expiration_date') {

            $getnetChargebacks->when(request('date_type'), function ($query, $search) {

                return $query->whereHas(
                    'sale.contestations',
                    function ($query) use ($search) {

                        $dateRange = FoxUtils::validateDateRange(request('date_range'));

                        $query->whereBetween(
                            $search,
                            [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
                        );

                    }
                );

            });

            if(request()->has('from_contestation')) {

                $getnetChargebacks->when(request('is_expired'), function ($query, $search) {

                    return $query->whereHas(
                        'sale.contestations',
                        function ($query) use ($search) {

                            if($search == 1){
                                return $query->whereDate('sale_contestations.expiration_date', '<=', date('Y-m-d'));
                            }

                            if($search == 2){
                                return $query->whereDate('sale_contestations.expiration_date', '>', date('Y-m-d'));
                            }

                        }
                    );

                });

            }

        }else{

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $getnetChargebacks->whereBetween(
                $filters['date_type'],
                [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
            );

        }

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
        }
        else {
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
                        : current(Hashids::connection('sale_id')->decode($item));
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

    public function getChargebackRateInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $approvedSales = (new SaleService)->getCreditCardApprovedSalesInPeriod($user, $startDate, $endDate);

        if ($approvedSales->count() < 50) {
            return 1.2;
        }

        $getnetChargebacks = GetnetChargeback::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->whereBetween(
                'start_date',
                [$startDate->format('Y-m-d') . ' 00:00:00', $endDate->format('Y-m-d') . ' 23:59:59']
            );
        })->where('user_id', $user->id);

        $chargebacksAmount = $getnetChargebacks->count();
        $approvedSalesAmount = $approvedSales->count();

        return $chargebacksAmount > 0 ? round(($chargebacksAmount * 100 / $approvedSalesAmount), 2) : 0;
    }

    public function getTotalChargebacksInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        return GetnetChargeback::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->whereBetween(
                'start_date',
                [$startDate->format('Y-m-d') . ' 00:00:00', $endDate->format('Y-m-d') . ' 23:59:59']
            );
        })->where('user_id', $user->id)
            ->get();
    }

    public function getChargebackTax($totalChargebacks, $totalApprovedSales)
    {
        if ($totalApprovedSales == 0)
            return '0,00%';

        $totalChargebackTax = $totalChargebacks > 0 ? number_format(($totalChargebacks * 100) / $totalApprovedSales, 2, ',', '.') . '%' : '0,00%';
        return $totalChargebackTax;
    }

}
