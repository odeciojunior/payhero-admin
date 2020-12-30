<?php

namespace Modules\PostBack\Http\Controllers;

use App\Jobs\ProcessShopifyPostbackJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Vinkla\Hashids\Facades\Hashids;

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
        try {
            $postBackLogModel = new PostbackLog();
            $projectModel = new Project();

            $requestData = $request->all();

            $postBackLogModel->create(
                [
                    'origin' => 5,
                    'data' => json_encode($requestData),
                    'description' => 'shopify-tracking',
                ]
            );

            $projectId = current(Hashids::decode($request->project_id));
            $project = $projectModel->find($projectId);

            if (!empty($project)) {
                ProcessShopifyPostbackJob::dispatch($projectId, $requestData)
                    ->onQueue('high');

                return response()->json(
                    [
                        'message' => 'success',
                    ],
                    200
                );
            } else {
                Log::warning('Shopify atualizar código de rastreio - projeto não encontrado');

                //projeto nao existe
                return response()->json(
                    [
                        'message' => 'project not found',
                    ],
                    200
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'error processing postback',
                ],
                200
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     * @throws PresenterException
     */
    public function postBackListener(Request $request)
    {
        $postBackLogModel = new PostbackLog();
        $projectModel = new Project();
        $userProjectModel = new UserProject();
        $shopifyIntegrationModel = new ShopifyIntegration();

        $requestData = $request->all();

        $postBackLogModel->create(
            [
                'origin' => 3,
                'data' => json_encode($requestData),
                'description' => 'shopify',
            ]
        );

        $projectId = current(Hashids::decode($request->project_id));

        if (empty($projectId)) {
            return response()->json(
                [
                    'message' => 'Projeto não encontrado',
                ],
                200
            );
        }
        //hash ok
        $project = $projectModel->find($projectId);

        if (empty($project)) {
            return response()->json(
                [
                    'message' => 'error',
                ],
                200
            );
        }

        $userProject = $userProjectModel->with('user')->where(
            [
                ['project_id', $project->id],
                ['type_enum', $userProjectModel->present()->getTypeEnum('producer')],
            ]
        )->first();

        if (empty($userProject->user)) {
            return response()->json(
                [
                    'message' => 'Usuario não encontrado',
                ],
                200
            );
        }

        try {
            $shopIntegration = $shopifyIntegrationModel->where('project_id', $project->id)->first();

            if (empty($shopIntegration)) {
                return response()->json(
                    [
                        'message' => 'Integração não encontrada',
                    ],
                    200
                );
            }

            $shopifyService = new ShopifyService($shopIntegration->url_store, $shopIntegration->token);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Dados do shopify inválidos, revise os dados informados',
                ],
                200
            );
        }

        if (!empty($requestData['variants']) && count($requestData['variants']) > 0) {
            $variant = current($requestData['variants']);
        }

        if (empty($variant['product_id'])) {
            $variant['product_id'] = $requestData['id'];
        }

        $shopifyService->importShopifyProduct($projectId, $userProject->user->id, $variant['product_id']);

        return response()->json(
            [
                'message' => 'success',
            ],
            200
        );
    }
}


