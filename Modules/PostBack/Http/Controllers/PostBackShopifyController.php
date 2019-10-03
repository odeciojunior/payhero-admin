<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function postBackTracking(Request $request)
    {
        $postBackLogModel = new PostbackLog();
        $salesModel       = new Sale();
        $projectModel     = new Project();

        $requestData = $request->all();

        $postBackLogModel->create([
                                      'origin'      => 3,
                                      'data'        => json_encode($requestData),
                                      'description' => 'shopify',
                                  ]);

        $projectId = current(Hashids::decode($request->project_id));

        if ($projectId) {
            //projectid ok

            $project = $projectModel->find($projectId);
            if ($project) {
                //projeto existe

                $shopifyOrder = $requestData['id'];

                $sale = $salesModel->with(['delivery'])
                                   ->where('shopify_order', $shopifyOrder)
                                   ->where('project_id', $project->id)
                                   ->first();

                if ($sale) {
                    //venda encontrada

                    foreach ($requestData['fulfillments'] as $fulfillment) {

                        if (!empty($fulfillment["tracking_number"])) {
                            if ($sale->delivery->tracking_code != $fulfillment["tracking_number"]) {

                                $sale->delivery->update([
                                                            'tracking_code' => $fulfillment["tracking_number"],
                                                        ]);

                                $sale->delivery->trackingHistories()->create([
                                                                                 'tracking_code' => $fulfillment["tracking_number"],
                                                                             ]);
                                event(new TrackingCodeUpdatedEvent($sale));
                            }

                            return response()->json([
                                                        'message' => 'success',
                                                    ], 200);
                        }
                    }
                } else {
                    Log::warning('Shopify atualizar código de rastreio - venda não encontrada');

                    //venda nao encontrada
                    return response()->json([
                                                'message' => 'sale not found',
                                            ], 200);
                }
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
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function postBackListener(Request $request)
    {

        $postBackLogModel        = new PostbackLog();
        $projectModel            = new Project();
        $userProjectModel        = new UserProject();
        $shopifyIntegrationModel = new ShopifyIntegration();

        $requestData = $request->all();

        $postBackLogModel->create([
                                      'origin'      => 3,
                                      'data'        => json_encode($requestData),
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


