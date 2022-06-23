<?php

namespace Modules\Core\Services\Reports;

use Exception;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\BrazilStatesService;
use Vinkla\Hashids\Facades\Hashids;

class ReportMarketingService
{
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

    public function getResumeMarketing($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $checkoutsCount = Checkout::where('project_id', $projectId)
                                    ->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                                    ->count();

        $salesCount = Sale::where('owner_id', auth()->user()->account_owner_id)
                            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->where('status', Sale::STATUS_APPROVED)
                            ->where('project_id', $projectId)
                            ->count();

        $salesValue = Sale::where('owner_id', auth()->user()->account_owner_id)
                            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->where('status', Sale::STATUS_APPROVED)
                            ->where('project_id', $projectId)
                            ->sum('original_total_paid_value');

        return [
            'checkouts_count' => number_format($checkoutsCount, 0, '.', '.'),
            'sales_count' => number_format($salesCount, 0, '.', '.'),
            'sales_value' => foxutils()->formatMoney($salesValue / 100),
            'conversion' => !empty($checkoutsCount) ? number_format(($salesCount * 100) / $checkoutsCount, 1, '.', '.') . '%' : '0%'
        ];
    }

    public function getSalesByState($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $data = Sale::select(DB::raw('delivery.state, count(*) as sales_amount, SUM(transaction.value) as value'))
                        ->join('transactions as transaction', function ($join) {
                            $join->on('transaction.sale_id', '=', 'sales.id');
                            $join->where('transaction.user_id', auth()->user()->account_owner_id);
                        })
                        ->join('deliveries as delivery', function ($join) {
                            $join->on('delivery.id', '=', 'sales.delivery_id');
                        })
                        ->where('sales.status', Sale::STATUS_APPROVED)
                        ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                        ->where('value', '>', 0)
                        ->where('sales.project_id', $projectId)
                        ->groupBy('delivery.state')
                        ->orderBy('value', 'DESC')
                        ->get()
                        ->toArray();

        $totalValue = 0;
        $totalSales = 0;
        foreach($data as $state) {
            $totalValue += $state['value'];
            $totalSales += $state['sales_amount'];
        }

        foreach($data as $key => &$state) {
            if(empty(BrazilStatesService::getStatePopulation($state['state']))){
                unset($data[$key]);
                continue;
            };

            if($filters['map_filter'] == 'density'){
                $salesPercentage = ($state['sales_amount'] / BrazilStatesService::getStatePopulation($state['state'])) * 100000;
                $state['percentage'] = number_format($salesPercentage, 2, '.', '.');
            }
            else {
                $state['percentage'] = number_format(($state['value'] * 100) / $totalValue, 2, '.', ',') . '%';
            }
            $state['value'] = foxutils()->formatMoney($state['value'] / 100);
        }

        if($filters['map_filter'] == 'density'){
            $percentage = array_column($data, 'percentage');
            array_multisort($percentage, SORT_DESC, $data);
        }
        $projectId = hashids_decode($filters['project_id']);

        return $data;
    }

    public function getMostFrequentSales($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $data = ProductPlanSale::select(DB::raw('product.photo, product.name, product.description, count(*) as sales_amount, sum(ifnull(transaction.value, 0)) as value'))
                        ->join('products as product', function ($join) {
                            $join->on('products_plans_sales.product_id', '=', 'product.id');
                        })
                        ->join('sales as sale', function ($join) {
                            $join->on('products_plans_sales.sale_id', '=', 'sale.id')
                                    ->where('sale.status', Sale::STATUS_APPROVED);
                        })
                        ->join('transactions as transaction', function ($join) {
                            $join->on('transaction.sale_id', '=', 'sale.id');
                            $join->where('transaction.user_id', auth()->user()->account_owner_id);
                        })
                        ->whereBetween('products_plans_sales.created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                        ->where('sale.project_id', $projectId)
                        ->where('transaction.user_id', auth()->user()->account_owner_id)
                        ->groupBy('product.id')
                        ->orderBy('value', 'DESC')
                        ->limit(10)
                        ->get()
                        ->toArray();

        foreach($data as &$product) {
            $product['sales_amount'] = number_format($product['sales_amount'], 0, '.', '.');
            $product['value'] = foxutils()->formatMoney($product['value'] / 100);
        }

        return $data;
    }

    public function getDevices($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $data = Sale::selectRaw("COUNT(*) AS total,
                        SUM(CASE WHEN checkout.is_mobile = 1 THEN 1 ELSE 0 END) AS count_mobile,
                        SUM(CASE WHEN checkout.is_mobile = 1 and sales.status = 1 THEN 1 ELSE 0 END) AS count_mobile_approved,
                        SUM(CASE WHEN checkout.is_mobile = 1 THEN transaction.value ELSE 0 END) AS value_mobile,
                        SUM(CASE WHEN checkout.is_mobile = 0 THEN 1 ELSE 0 END) AS count_desktop,
                        SUM(CASE WHEN checkout.is_mobile = 0 and sales.status = 1 THEN 1 ELSE 0 END) AS count_desktop_approved,
                        SUM(CASE WHEN checkout.is_mobile = 0 THEN transaction.value ELSE 0 END) AS value_desktop
                    ")
                    ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                    ->join('checkouts as checkout', function ($join) {
                        $join->on('sales.checkout_id', '=', 'checkout.id');
                    })
                    ->join('transactions as transaction', function ($join) {
                        $join->on('transaction.sale_id', '=', 'sales.id');
                        $join->where('transaction.user_id', auth()->user()->account_owner_id);
                    })
                    ->where('owner_id', auth()->user()->account_owner_id)
                    ->where('sales.project_id', $projectId)
                    ->first()
                    ->toArray();

        if(empty($data['count_mobile'])){
            $data['count_mobile'] = 0;
            $data['count_mobile_approved'] = 0;
            $data['percentage_mobile'] = '0%';
        } else {
            $data['percentage_mobile'] = number_format(($data['count_mobile'] * 100) / $data['total'], 2, '.', ',') . '%';
        }

        if(empty($data['count_desktop'])){
            $data['count_desktop'] = 0;
            $data['count_desktop_approved'] = 0;
            $data['percentage_desktop'] = '0%';
        } else {
            $data['percentage_desktop'] = number_format(($data['count_desktop'] * 100) / $data['total'], 2, '.', ',') . '%';;
        }

        $data['value_mobile'] = $data['value_mobile'] > 0 ? foxutils()->formatMoney($data['value_mobile'] / 100) : 'R$ 0,00';
        $data['value_desktop'] = $data['value_desktop'] > 0 ? foxutils()->formatMoney($data['value_desktop'] / 100) : 'R$ 0,00';

        return [
            'mobile' => [
                'total' => $data['count_mobile'],
                'approved' => $data['count_mobile_approved'],
                'value' => $data['value_mobile'],
                'percentage' => $data['percentage_mobile']
            ],
            'desktop' => [
                'total' => $data['count_desktop'],
                'approved' => $data['count_desktop_approved'],
                'value' => $data['value_desktop'],
                'percentage' => $data['percentage_desktop']
            ]
        ];
    }

    public function getOperationalSystems($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $data = Checkout::select(DB::raw('os_enum, count(*) as sales_amount'))
                            ->leftJoin('sales as s', 's.checkout_id', '=', 'checkouts.id')
                            ->where('s.status', Sale::STATUS_APPROVED)
                            ->where('s.owner_id', auth()->user()->account_owner_id)
                            ->whereBetween('s.start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->where('checkouts.project_id', $projectId)
                            ->groupBy('os_enum')
                            ->orderBy('sales_amount', 'desc')
                            ->get()
                            ->toArray();

        $salesAmount = 0;

        foreach($data as $key => &$operationalSystem) {
            if(!in_array($operationalSystem['os_enum'], [
                            Checkout::OPERATIONAL_SYSTEM_ANDROID,
                            Checkout::OPERATIONAL_SYSTEM_IOS,
                            Checkout::OPERATIONAL_SYSTEM_WINDOWS,
                            Checkout::OPERATIONAL_SYSTEM_LINUX
            ])){
                unset($data[$key]);
                continue;
            }
            $salesAmount += $operationalSystem['sales_amount'];
        }

        foreach($data as &$operationalSystem) {
            $operationalSystem['description'] = (new Checkout)->present()->getOperationalSystemName($operationalSystem['os_enum']);
            $operationalSystem['percentage'] = number_format(($operationalSystem['sales_amount'] * 100) / $salesAmount, 1, '.', ',') . '%';
            unset($operationalSystem['id_code']);
            unset($operationalSystem['os_enum']);
            unset($operationalSystem['sales_amount']);
        }

        return $data;
    }

    public function getStateDetail($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $totalValue = Sale::join('transactions as transaction', function ($join) {
                                $join->on('transaction.sale_id', '=', 'sales.id');
                                $join->where('transaction.user_id', auth()->user()->account_owner_id);
                            })
                            ->join('deliveries as delivery', function ($join) use ($filters) {
                                $join->on('delivery.id', '=', 'sales.delivery_id')
                                    ->where('delivery.state', $filters['state']);
                            })
                            ->where('sales.status', Sale::STATUS_APPROVED)
                            ->where('project_id', $projectId)
                            ->where('owner_id', auth()->user()->account_owner_id)
                            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->sum('transaction.value');

        $totalSales = Sale::join('deliveries as delivery', function ($join) use ($filters) {
                            $join->on('delivery.id', '=', 'sales.delivery_id')
                                ->where('delivery.state', $filters['state']);
                        })
                        ->where('project_id', $projectId)
                        ->where('sales.status', Sale::STATUS_APPROVED)
                        ->where('owner_id', auth()->user()->account_owner_id)
                        ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                        ->count();

        $accesses = Checkout::where('project_id', $projectId)
                            ->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                            ->count();

        return [
            'total_value' => foxutils()->formatMoney($totalValue / 100),
            'total_sales' => number_format($totalSales, 0, '.', '.'),
            'accesses' => number_format($accesses, 0, '.', '.'),
            'conversion' => $accesses > 0 ? number_format(($totalSales * 100) / $accesses, 1, '.', ',') . '%' : '0%'
        ];
    }

    public function getResumeCoupons($filters)
    {
        $dateRange = foxutils()->validateDateRange($filters["date_range"]);
        $projectId = hashids_decode($filters['project_id']);

        $cacheName = 'coupons-resume-' . $projectId . '-' . json_encode($dateRange);

        $coupons = cache()->remember($cacheName, 180, function() use($projectId, $dateRange){
            return Sale::select(DB::raw('sales.cupom_code as coupon, COUNT(*) as amount'))
                            ->where('status', Sale::STATUS_APPROVED)
                            ->where('project_id', $projectId)
                            ->where('sales.cupom_code', '<>', '')
                            ->whereBetween('sales.start_date', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
                            ->groupBy('sales.cupom_code')
                            ->orderByDesc('amount')
                            ->limit(4)
                            ->get();
        });

        $total = 0;
        foreach($coupons as $coupon)
        {
            $total += $coupon->amount;
        }

        $index = 0;
        foreach($coupons as $coupon)
        {
            $coupon->percentage = round(number_format(($coupon->amount * 100) / $total, 2, '.', ','), 1, PHP_ROUND_HALF_UP).'%';
            $coupon->color = $this->getColors($index);
            $coupon->hexadecimal = $this->getColors($index, true);

            $index++;
        }

        $couponsArray = $coupons->toArray();

        return [
            'coupons' => $couponsArray,
            'total' => $total
        ];
    }

    public function getResumeRegions($filters)
    {
        try {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $projectId = current(Hashids::decode($filters['project_id']));

            $regions = Checkout::select(
                DB::raw('
                    ip_state as region,
                    COUNT(DISTINCT CASE WHEN status_enum = 1 then id end) as access,
                    COUNT(DISTINCT CASE WHEN status_enum = 4 then id end) as conversion
                ')
            )
            ->whereIn('checkouts.status_enum', [ Checkout::STATUS_ACCESSED, Checkout::STATUS_SALE_FINALIZED ])
            ->whereBetween('checkouts.created_at', [ $dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59' ])
            ->where('checkouts.project_id', $projectId)
            ->groupBy('region')
            ->get()
            ->toArray();

            $total = 0;
            foreach($regions as $region)
            {
                $total += $region['access'] + $region['conversion'];
            }

            foreach($regions as $region)
            {
                $region['percentage_access'] = round(number_format(($region['access'] * 100) / $total, 2, '.', ','), 1, PHP_ROUND_HALF_UP);
                $region['percentage_conversion'] = round(number_format(($region['conversion'] * 100) / $total, 2, '.', ','), 1, PHP_ROUND_HALF_UP);
            }

            return $regions;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getResumeOrigins($filters)
    {
        try {
            $projectId = hashids_decode($filters['project_id']);

            $userId = auth()->user()->account_owner_id;
            $status = Sale::STATUS_APPROVED;
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);

            $originsData = Sale::select(DB::raw('count(*) as sales_amount, SUM(transaction.value) as value, checkout.'.$filters['origin'].' as origin'))
            ->leftJoin('transactions as transaction', function ($join) use ($userId) {
                $join->on('transaction.sale_id', '=', 'sales.id');
                $join->where('transaction.user_id', $userId);
            })
            ->leftJoin('checkouts as checkout', function ($join) {
                $join->on('checkout.id', '=', 'sales.checkout_id');
            })
            ->where('sales.status', $status)
            ->where('sales.project_id', $projectId)
            ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
            ->whereNotIn('checkout.'.$filters['origin'], ['', 'null'])
            ->whereNotNull('checkout.'.$filters['origin'])
            ->groupBy('checkout.'.$filters['origin'])
            ->orderBy('sales_amount', 'DESC');

            return $originsData;
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
