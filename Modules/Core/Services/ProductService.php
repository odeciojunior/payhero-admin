<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleAdditionalCustomerInformation;
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

        if (!empty($projectId) && (!empty($project->shopify_id) || !empty($project->woocommerce_id))) {
            return $productModel
            ->with('productsPlans')
            ->where('user_id', auth()->user()->account_owner_id)
            ->where('project_id', $projectId)
            ->take(16)
            ->get();
        } else {
            return $productModel
            ->with('productsPlans')
            ->where('user_id', auth()->user()->account_owner_id)
            ->where('shopify', 0)
            ->take(16)
            ->get();
        }
    }

    public function getProducts()
    {

    }

    public function getProductsFilter(int $projectId, string $product, bool $variants = false)
    {
        $productModel = new Product();
        $projectModel = new Project();
        $project = $projectModel->find($projectId);

        if ($variants == true) {
            if (!empty($product)) {
                return $productModel
                ->select('shopify_id')
                ->distinct()
                ->where('user_id', auth()->user()->account_owner_id)
                ->where('project_id', $projectId)
                ->where('name', 'like', '%'. $product .'%')
                ->take(16)
                ->get();
            } else {
                return $productModel
                ->select('shopify_id')
                ->distinct()
                ->where('user_id', auth()->user()->account_owner_id)
                ->where('project_id', $projectId)
                ->take(16)
                ->get();
            }
        } else {
            if (!empty($projectId) && ( !empty($project->shopify_id) || !empty($project->woocommerce_id) ) ) {
                if (!empty($product)) {
                    return $productModel
                    ->with('productsPlans')
                    ->where('user_id', auth()->user()->account_owner_id)
                    ->where('project_id', $projectId)
                    ->where('name', 'like', '%'. $product .'%')
                    ->take(16)
                    ->get();
                } else {
                    return $productModel
                    ->with('productsPlans')
                    ->where('user_id', auth()->user()->account_owner_id)
                    ->where('project_id', $projectId)
                    ->take(16)
                    ->get();
                }
            } else {
                if (!empty($product)) {
                    return $productModel
                    ->with('productsPlans')
                    ->where('user_id', auth()->user()->account_owner_id)
                    ->where('name', 'like', '%'. $product .'%')
                    ->where('shopify', 0)
                    ->take(16)
                    ->get();
                } else {
                    return $productModel
                    ->with('productsPlans')
                    ->where('user_id', auth()->user()->account_owner_id)
                    ->where('shopify', 0)
                    ->take(16)
                    ->get();
                }
            }
        }
    }

    public function getProductsBySale($saleParam)
    {
        if ($saleParam instanceof Sale) {
            $sale = $saleParam;
            $sale->loadMissing([
                'productsPlansSale.tracking',
                'productsPlansSale.product',
                'productsSaleApi'
            ]);
        } else {
            if (is_int($saleParam)) {
                $saleModel = new Sale();
                $sale = $saleModel->with([
                    'productsPlansSale.tracking',
                    'productsPlansSale.product',
                    'productsSaleApi'
                ])->find($saleParam);
            } else {
                if (is_string($saleParam)) {
                    $saleModel = new Sale();
                    $saleId = current(Hashids::connection('sale_id')->decode($saleParam));
                    $sale = $saleModel->with([
                        'productsPlansSale.tracking',
                        'productsPlansSale.product',
                        'productsSaleApi'
                    ])->find($saleId);
                }
            }
        }

        $productsSale = collect();

        if (!empty($sale)) {
            if ($sale->api_flag) {
                foreach ($sale->productsSaleApi as $productsApi) {
                    $product = $productsApi->toArray();

                    $product['tracking_id'] = '';
                    $product['tracking_code'] = '';
                    $product['tracking_status_enum'] = 'Não Informado';
                    $product['tracking_created_at'] = '';

                    $product['sale_status'] = $sale->status;
                    $product['amount'] = $productsApi->quantity;
                    $product['sale_id'] = $productsApi->sale_id;

                    $productsSale->add((object)$product);
                }
            } else {
                foreach ($sale->productsPlansSale as $productsPlanSale) {
                    $product = $productsPlanSale->product()->withTrashed()->first()->toArray();
                    $tracking = $productsPlanSale->tracking;

                    if(!is_null($product['deleted_at'])) {
                        $product['name'] .= ' (Excluído)';
                    }

                    $product['product_plan_sale_id'] = $productsPlanSale->id;
                    $product['sale_status'] = $sale->status;
                    $product['amount'] = $productsPlanSale->amount;

                    if (!empty($tracking)) {
                        $trackingCode = $tracking->tracking_code == "CLOUDFOX000XX" ? '' : $tracking->tracking_code;
                        $product['tracking_id'] = Hashids::encode($tracking->id);
                        $product['tracking_code'] = $trackingCode;
                        $product['tracking_status_enum'] = $tracking->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()->getTrackingStatusEnum($tracking->tracking_status_enum)) : 'Não Informado';
                        $product['tracking_created_at'] = Carbon::parse($tracking->created_at)->format('d/m/Y H:i:s');
                    } else {
                        $product['tracking_id'] = '';
                        $product['tracking_code'] = '';
                        $product['tracking_status_enum'] = 'Não Informado';
                        $product['tracking_created_at'] = '';
                    }

                    $product['photo'] = FoxUtils::checkFileExistUrl($product['photo']) ? $product['photo'] : 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/produto.png';
                    $product['custom_products'] = SaleAdditionalCustomerInformation::select('id', 'type_enum', 'value', 'file_name', 'line')
                        ->where('sale_id', $productsPlanSale->sale_id)->where('plan_id', $productsPlanSale->plan_id)
                        ->where('product_id', $productsPlanSale->product_id)->orderBy('line', 'asc')->orderBy('order', 'asc')->get();
                    $product['sale_id'] = $productsPlanSale->sale_id;
                    $productsSale->add((object)$product);
                }
            }
        }

        return $productsSale;
    }

    public function getTicketPlans($sale)
    {
        if (!empty($sale)) {
            $products = [];
            $sale->load(['plansSales.plan.productsPlans.product']);

            foreach ($sale->plansSales as $planSale) {
                foreach ($planSale->plan->productsPlans as $productPlan) {
                    $products[] = [
                        'name' => $productPlan->amount * $planSale->amount . 'x ' . $planSale->plan->name,
                    ];
                }
            }

            return $products;
        }

        return [];
    }
}
