<?php

namespace Modules\PostBack\Http\Controllers;

use App\Entities\Project;
use App\Entities\Sale;
use Illuminate\Http\Request;
use App\Entities\PostbackLog;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use App\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

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
        $salesModel   = new Sale();
        $projectModel = new Project();

        $requestData = $request->all();

        $projectId = current(Hashids::decode($request->project_id));

        if ($projectId) {
            //projectid ok

            $project = $projectModel->find($projectId);
            if ($project) {
                //projeto existe

                $shopifyOrder = $requestData['id'];

                $sale = $salesModel->with(['delivery', 'delivery.trackingHistories'])
                                   ->where('shopify_order', $shopifyOrder)
                                   ->where('project', $project->id)
                                   ->first();

                if ($sale) {
                    //venda encontrada

                    foreach ($requestData['fulfillments'] as $fulfillment) {

                        if (!empty($fulfillment["tracking_number"])) {
                            if ($sale->getRelation('delivery')->tracking_code != $fulfillment["tracking_number"]) {

                                $sale->getRelation('delivery')->update([
                                                                           'tracking_code' => $fulfillment["tracking_number"],
                                                                       ]);

                                $sale->getRelation('delivery')->trackingHistories()->create([
                                                                                                'tracking_code' => $fulfillment["tracking_number"],
                                                                                            ]);
                            }

                            return response()->json([
                                                        'message' => 'success',
                                                    ], 200);
                        }
                    }
                } else {
                    //venda nao encontrada
                    return response()->json([
                                                'message' => 'error',
                                            ], 400);
                }
            } else {
                //projeto nao existe
                return response()->json([
                                            'message' => 'error',
                                        ], 400);
            }
        } else {
            //projectid errado
            return response()->json([
                                        'message' => 'error',
                                    ], 400);
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

        $project = $projectModel->find($projectId);

        // Log::warning($projectId);

        if (!$project) {
            Log::warning('error', 'projeto não encontrado no retorno do shopify, projeto = ' . $request->project_id);

            return response()->json([
                                        'message' => 'error',
                                    ], 400);
        }

        $userProject = $userProjectModel->where([
                                                    ['project', $project->id],
                                                    ['type', 'producer'],
                                                ])->first();

        try {
            $shopIntegration = $shopifyIntegrationModel->where('project', $project->id)->first();

            $shopifyService = new ShopifyService($shopIntegration->url_store, $shopIntegration->token);
        } catch (\Exception $e) {
            return response()->json([
                                        'message' => 'Dados do shopify inválidos, revise os dados informados',
                                    ], 400);
        }

        //foreach ($requestData['variants'] as $variant) {
        $variant = current($requestData['variants']);

        $shopifyService->importShopifyProduct($projectId, $userProject->user, $variant['product_id']);

        //}

        return response()->json([
                                    'message' => 'success',
                                ], 200);
    }
}
