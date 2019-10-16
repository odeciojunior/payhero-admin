<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ProductService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

/**
 * Class PostBackShopifyController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackShopifyController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postBackTracking(Request $request)
    {
        $postBackLogModel = new PostbackLog();
        $salesModel = new Sale();
        $projectModel = new Project();
        $productPlanSaleModel = new ProductPlanSale();
        $productService = new ProductService();

        $requestData = $request->all();

        $postBackLogModel->create([
            'origin' => 5,
            'data' => json_encode($requestData),
            'description' => 'shopify-tracking',
        ]);

        $projectId = current(Hashids::decode($request->project_id));

        if ($projectId) {
            //projectid ok

            $project = $projectModel->find($projectId);
            if ($project) {
                //projeto existe

                $shopifyOrder = $requestData['id'];

                $sale = $salesModel->with(['productsPlansSale'])
                    ->where('shopify_order', $shopifyOrder)
                    ->where('project_id', $project->id)
                    ->first();

                //venda encontrada
                if ($sale) {
                    //obtem os produtos da venda
                    $saleProducts = $productService->getProductsBySale(Hashids::connection('sale_id')->encode($sale->id));
                    foreach ($requestData['fulfillments'] as $fulfillment) {
                        if (!empty($fulfillment["tracking_number"])) {
                            //percorre os produtos que vieram no postback
                            foreach ($fulfillment["line_items"] as $line_item) {
                                //verifica se existem produtos na venda com mesmo variant_id e com mesma quantidade vendida
                                $products = $saleProducts->where('shopify_variant_id', $line_item["variant_id"])
                                    ->where('amount', $line_item["quantity"]);
                                if ($products->count()) {
                                    foreach ($products as &$product) {
                                        //caso exista, verifica se o codigo que de rastreio que veio no postback e diferente
                                        //do que esta na tabela
                                        $productPlanSale = $productPlanSaleModel->find($product->product_plan_sale_id);
                                        if (isset($productPlanSale)) {
                                            //caso seja diferente, atualiza o registro e dispara o e-mail
                                            if ($productPlanSale->tracking_code != $fulfillment["tracking_number"]) {
                                                $productPlanSale->update(['tracking_code' => $fulfillment["tracking_number"]]);
                                                //atualiza no array de produtos para enviar no email
                                                $product->tracking_code = $fulfillment["tracking_number"];
                                                event(new TrackingCodeUpdatedEvent($sale, $productPlanSale, $saleProducts));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return response()->json([
                        'message' => 'success',
                    ], 200);
                }
                return response()->json([
                    'message' => 'sale not found',
                ], 200);
            } else {
                Log::warning('Shopify atualizar código de rastreio - projeto não encontrado');

                //projeto nao existe
                return response()->json([
                    'message' => 'project not found',
                ], 200);
            }
        } else {
            Log::warning('Shopify atualizar código de rastreio - projeto não encontrado');
            //projectid errado
            return response()->json([
                'message' => 'project not found',
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     */
    public function postBackListener(Request $request)
    {

        $postBackLogModel = new PostbackLog();
        $projectModel = new Project();
        $userProjectModel = new UserProject();
        $shopifyIntegrationModel = new ShopifyIntegration();

        $requestData = $request->all();

        $postBackLogModel->create([
            'origin' => 3,
            'data' => json_encode($requestData),
            'description' => 'shopify',
        ]);

        $projectId = current(Hashids::decode($request->project_id));

        if ($projectId) {
            //hash ok
            $project = $projectModel->find($projectId);

            if (!$project) {
                Log::warning('projeto não encontrado no retorno do shopify, projeto = ' . $request->project_id);

                return response()->json([
                    'message' => 'error',
                ], 400);
            }

            $userProject = $userProjectModel->where([
                ['project_id', $project->id],
                ['type', 'producer'],
            ])->first();

            try {
                $shopIntegration = $shopifyIntegrationModel->where('project_id', $project->id)->first();

                $shopifyService = new ShopifyService($shopIntegration->url_store, $shopIntegration->token);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Dados do shopify inválidos, revise os dados informados',
                ], 400);
            }

            $variant = current($requestData['variants']);

            $shopifyService->importShopifyProduct($projectId, $userProject->user->id, $variant['product_id']);

            return response()->json([
                'message' => 'success',
            ], 200);
        } else {
            //hash invalido
            return response()->json([
                'message' => 'Projeto não encontrado',
            ], 400);
        }
    }
}


