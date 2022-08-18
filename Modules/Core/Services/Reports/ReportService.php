<?php

namespace Modules\Core\Services\Reports;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\DiscountCoupon;

class ReportService
{
    public function getDashboardChartData($companyId)
    {
        try {
            $cacheName = 'dashboard-chart-'.json_encode($companyId);
            return cache()->remember($cacheName, 60, function() use ($companyId) {
                $labelList    = [];
                $dataFormated = Carbon::now()->subMonth()->subDays(5);
                $endDate      = Carbon::now();

                while ($endDate->greaterThanOrEqualTo($dataFormated)) {
                    array_push($labelList, $endDate->format("d/m"));
                    $endDate = $endDate->subDays(5);
                    if ($endDate->diffInDays($dataFormated) < 1) {
                        $endDate = $endDate->subDays($endDate->diffInDays($dataFormated));
                        array_push($labelList, $endDate->format("d/m"));
                        break;
                    }
                }

                $startDate = Carbon::now()
                    ->subMonth()
                    ->subDays(5)
                    ->format("Y-m-d");
                $endDate = Carbon::now()
                    ->addDay()
                    ->format("Y-m-d");

                $orders = Sale::select(
                    DB::raw("count(*) as count, DATE(sales.end_date) as date, SUM(transaction.value) as value")
                )
                    ->leftJoin("transactions as transaction", function ($join) use ($companyId) {
                        $join->on("transaction.sale_id", "=", "sales.id");
                        $join->where("transaction.company_id", $companyId);
                    })
                    ->where("sales.status", Sale::STATUS_APPROVED)
                    ->whereBetween("end_date", [$startDate, $endDate])
                    ->groupBy("date");

                $orders = $orders->get()->toArray();
                $labelList = array_reverse($labelList);
                $valueData = [];
                foreach ($labelList as $key => $label) {
                    $valueData[$key] = 0;

                    foreach ($orders as $order) {
                        if (
                            $label == Carbon::parse($order["date"])->format("d/m") ||
                            Carbon::createFromFormat("d/m", $label)
                                ->subDay()
                                ->format("d/m") == Carbon::parse($order["date"])->format("d/m") ||
                            Carbon::createFromFormat("d/m", $label)
                                ->subDays(2)
                                ->format("d/m") == Carbon::parse($order["date"])->format("d/m") ||
                            Carbon::createFromFormat("d/m", $label)
                                ->subDays(3)
                                ->format("d/m") == Carbon::parse($order["date"])->format("d/m") ||
                            Carbon::createFromFormat("d/m", $label)
                                ->subDays(4)
                                ->format("d/m") == Carbon::parse($order["date"])->format("d/m")
                        ) {
                            if ($order["value"] >= 100) {
                                $order["value"] = (int) substr($order["value"], 0, -2);
                            }

                            $valueData[$key] += (int) foxutils()->onlyNumbers($order["value"]);
                        }
                    }
                }

                return [
                    "label_list" => $labelList,
                    "value_data" => $valueData,
                    "currency" => 'R$',
                ];
            });
        } catch (Exception $e) {
            report($e);
            return ['message' => 'Não foi possível verificar todos os valores totais de venda'];
        }
    }

    public static function getProjectsWithCheckouts(){
        return Checkout::select('checkouts.project_id')
            ->distinct()
            ->leftjoin('checkout_configs','checkout_configs.project_id','checkouts.project_id')
            ->join('companies','companies.id','checkout_configs.company_id')
            ->where('companies.user_id',auth()->user()->getAccountOwnerId())
            ->get();
    }

    public static function getProjectsWithCoupons(){
        return DiscountCoupon::select('discount_coupons.project_id')
            ->distinct()
            ->leftjoin('checkout_configs','checkout_configs.project_id','discount_coupons.project_id')
            ->join('companies','companies.id','checkout_configs.company_id')
            ->where('companies.user_id',auth()->user()->getAccountOwnerId())
            ->get();
    }

    public static function getProjectsWithPendingBalance(){
        return Sale::select('sales.project_id')
            ->distinct()
            ->leftjoin('transactions','transactions.sale_id','sales.id')
            ->where('transactions.user_id',auth()->user()->getAccountOwnerId())
            ->where('transactions.STATUS','pending')
            ->get();
    }

    public static function getProjectsWithBlockedBalance(){
        return Sale::select('sales.project_id')
            ->distinct()
            ->leftjoin('block_reason_sales','block_reason_sales.sale_id','sales.id')
            ->where('sales.owner_id',auth()->user()->getAccountOwnerId())
            ->where('block_reason_sales.status',1)
            ->get();
    }

}

