<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;

class ProductService
{
    /**
     * @param int $projectId
     * @return mixed
     * Retorna produtos
     */
    public function getProductsMyProject(int $projectId)
    {
        $productModel = new Product();
        $projectModel = new Project();
        $project = $projectModel->find($projectId);
        if (!empty($projectId) && !empty($project->shopify_id)) {
            return $productModel->where('user_id', auth()->user()->id)
                ->where('shopify', 1)
                ->whereHas('productsPlans.plan', function ($queryPlan) use ($projectId) {
                    $queryPlan->where('project_id', $projectId);
                })->get();
        } else {
            return $productModel->where('user_id', auth()->user()->id)
                ->where('shopify', 0)->get();
        }
    }

    public function getProductsBySale($saleId)
    {
        $saleModel = new Sale();
        $productPlanSaleModel = new ProductPlanSale();

        $saleId = current(Hashids::connection('sale_id')->decode($saleId));
        $sale = $saleModel->with(['plansSales'])->find($saleId);

        $productsSale = collect();
        /** @var PlanSale $planSale */
        foreach ($sale->plansSales as $planSale) {
            /** @var ProductPlan $productPlan */
            foreach ($planSale->plan->productsPlans as $productPlan) {
                $productPlanSale = $productPlan->product()
                    ->first()->productsPlanSales->where('sale_id', $sale->id)
                    ->first();
                $product = $productPlan->product()->first();
                $product['product_plan_sale_id'] = $productPlanSale->id;
                $product['sale_status'] = $sale->status;
                $product['amount'] = $productPlan->amount * $planSale->amount;
                $product['tracking_code'] = $productPlanSale ? $productPlanSale->tracking_code ?? '' : '';
                $product['tracking_status_enum'] = $productPlanSale ?  $productPlanSale->tracking_status_enum != null ?
                    Lang::get('definitions.enum.product_plan_sale.tracking_status_enum.' . $productPlanSaleModel->present()
                            ->getStatusEnum($productPlanSale->tracking_status_enum)) : 'Não informado' : 'Não informado';
                $productsSale->add($product);
            }
        }

        return $productsSale;
    }

    /**
     * @param $saleId
     * @return \Illuminate\Support\Collection
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function getProductsBySaleId($saleId)
    {
        $saleModel = new Sale();
        $productPlanSaleModel = new ProductPlanSale();

        $sale = $saleModel->with(['plansSales'])->find($saleId);

        $productsSale = collect();
        /** @var PlanSale $planSale */
        foreach ($sale->plansSales as $planSale) {
            /** @var ProductPlan $productPlan */
            foreach ($planSale->plan->productsPlans as $productPlan) {
                $productPlanSale = $productPlan->product()
                                               ->first()->productsPlanSales->where('sale_id', $sale->id)
                                                                           ->first();
                $product = $productPlan->product()->first();
                $product['product_plan_sale_id'] = $productPlanSale->id;
                $product['sale_status'] = $sale->status;
                $product['amount'] = $productPlan->amount * $planSale->amount;
                $product['tracking_code'] = $productPlanSale ? $productPlanSale->tracking_code ?? '' : '';
                $product['tracking_status_enum'] = $productPlanSale ?  $productPlanSale->tracking_status_enum != null ?
                    Lang::get('definitions.enum.product_plan_sale.tracking_status_enum.' . $productPlanSaleModel->present()
                                                                                                                ->getStatusEnum($productPlanSale->tracking_status_enum)) : 'Não informado' : 'Não informado';
                $productsSale->add($product);
            }
        }

        return $productsSale;
    }
}
