<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SaleService
 * @package Modules\Core\Services
 */
class SaleService
{
    public function getSales($filters, $paginate = true)
    {
        $companyModel = new Company();
        $clientModel = new Client();
        $transactionModel = new Transaction();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)
            ->pluck('id')
            ->toArray();

        $transactions = $transactionModel->with([
            'sale',
            'sale.project',
            'sale.client',
            'sale.plansSales',
            'sale.plansSales.plan',
            'sale.plansSales.plan.products',
            'sale.plansSales.plan.project',
        ])->whereIn('company_id', $userCompanies)
            ->whereNull('invitation_id');

        if (!empty($filters["project"])) {
            $projectId = current(Hashids::decode($filters["project"]));
            $transactions->whereHas('sale', function ($querySale) use ($projectId) {
                $querySale->where('project_id', $projectId);
            });
        }

        if (!empty($filters["transaction"])) {
            $saleId = current(Hashids::connection('sale_id')
                ->decode(str_replace('#', '', $filters["transaction"])));

            $transactions->whereHas('sale', function ($querySale) use ($saleId) {
                $querySale->where('id', $saleId);
            });
        }

        if (!empty($filters["client"])) {
            $customers = $clientModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
            $transactions->whereHas('sale', function ($querySale) use ($customers) {
                $querySale->whereIn('client_id', $customers);
            });
        }

        if (!empty($filters["payment_method"])) {
            $forma = $filters["payment_method"];
            $transactions->whereHas('sale', function ($querySale) use ($forma) {
                $querySale->where('payment_method', $forma);
            });
        }

        if (empty($filters['status'])) {
            $status = [1, 2, 4, 6];
        } else {
            $status = [$filters["status"]];
        }

        $transactions->whereHas('sale', function ($querySale) use ($status) {
            $querySale->whereIn('status', $status);
        });

        //tipo da data e periodo obrigatorio
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dateType = $filters["date_type"];

        $transactions->whereHas('sale', function ($querySale) use ($dateRange, $dateType) {
            $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
        });


        if ($paginate) {
            return $transactions->orderBy('id', 'DESC')->paginate(10);
        }
        return [];
    }


    /**
     * @param Sale $sale
     * @return float|int
     */
    public function getSubTotal(Sale $sale)
    {
        $subTotal = 0;
        foreach ($sale->plansSales as $planSale) {
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan()->first()->price) * $planSale->amount;
        }

        return $subTotal;
    }

    /**
     * @param Sale $sale
     * @return array
     */
    public function getPagarmeItensList(Sale $sale)
    {
        $itens = [];

        foreach ($sale->plansSales as $key => $planSale) {
            $itens[] = [
                'id' => '#' . Hashids::encode($planSale->plan->id),
                'title' => $planSale->plan->name,
                'unit_price' => str_replace('.', '', $planSale->plan->price),
                'quantity' => $planSale->amount,
                'tangible' => true,
            ];
        }

        return $itens;
    }

    /**
     * @return array
     */
    public function getProducts($saleId = null)
    {
        try {
            $saleModel = new Sale();
            $productModel = new Product();
            $planSaleModel = new PlanSale();

            if ($saleId) {

                $sale = $saleModel->with([
                    'plansSales.plan.products',
                ])->find($saleId);

                $plansIds = $sale->plansSales->pluck('plan_id')->toArray();

                $products = $productModel->whereHas('productsPlans', function ($query) use ($plansIds) {
                    $query->whereIn('plan_id', $plansIds);
                })->get();

                return $products;
            } else {
                return null;
            }
        } catch (Exception $ex) {

            //thowl
        }
    }
}
