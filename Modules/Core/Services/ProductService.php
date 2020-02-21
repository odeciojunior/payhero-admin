<?php

namespace Modules\Core\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Laracasts\Presenter\Exceptions\PresenterException;
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
        $project      = $projectModel->find($projectId);
        if (!empty($projectId) && !empty($project->shopify_id)) {
            return $productModel->where('user_id', auth()->user()->account_owner_id)
                                ->where('project_id', $projectId)->get();
        } else {
            return $productModel->where('user_id', auth()->user()->account_owner_id)
                                ->where('shopify', 0)->get();
        }
    }

    public function getProductsBySale($saleParam)
    {
        if ($saleParam instanceof Sale) {
            $sale = $saleParam;
        } else if (is_int($saleParam)) {
            $saleModel = new Sale();
            $sale = $saleModel->with(['productsPlansSale.tracking'])
                ->find($saleParam);
        } else if (is_string($saleParam)) {
            $saleModel = new Sale();
            $saleId = current(Hashids::connection('sale_id')->decode($saleParam));
            $sale = $saleModel->with(['productsPlansSale.tracking'])
                ->find($saleId);
        }

        $productsSale = collect();

        if (!empty($sale)) {
            foreach ($sale->productsPlansSale as $productsPlanSale) {
                $product = $productsPlanSale->product;
                $tracking = $productsPlanSale->tracking;
                $product['product_plan_sale_id'] = $productsPlanSale->id;
                $product['sale_status'] = $sale->status;
                $product['amount'] = $productsPlanSale->amount;
                $product['tracking_id'] = Hashids::encode($tracking->id ?? '');
                $product['tracking_code'] = $tracking->tracking_code ?? '';
                $product['tracking_status_enum'] = $tracking
                    ? __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()
                            ->getTrackingStatusEnum($tracking->tracking_status_enum))
                    : 'Não informado';
                $productsSale->add($product);
            }
        }

        return $productsSale;
    }

    public function getTicketProducts($sale)
    {
        if (!empty($sale)) {
            $products = [];
            $sale->load(['plansSales.plan.productsPlans.product']);

            foreach ($sale->plansSales as $planSale) {
                foreach ($planSale->plan->productsPlans as $productPlan) {
                    $products[] = [
                        'name' => $productPlan->product->name,
                    ];
                }
            }

            return $products;
        }

        return [];
    }
}
