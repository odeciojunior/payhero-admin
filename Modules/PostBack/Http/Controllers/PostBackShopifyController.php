<?php

namespace Modules\PostBack\Http\Controllers;

use App\Jobs\ProcessShopifyPostbackJob;
use App\Jobs\ProcessShopifyTrackingPostbackJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PostBackShopifyController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackShopifyController extends Controller
{

    public function postBackTracking(Request $request)
    {
        try {
            $requestData = $request->all();
            $projectId = current(Hashids::decode($request->project_id));

            ProcessShopifyTrackingPostbackJob::dispatch($projectId, $requestData)->onQueue(
                "postback-shopify-tracking"
            );

            return response()->json(["message" => "success"]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "error processing postback"]);
        }
    }

    public function postBackListener(Request $request)
    {
        try {
            $postBackLogModel = new PostbackLog();
            $projectModel = new Project();

            $requestData = $request->all();

            $postBackLogModel->create([
                "origin" => 3,
                "data" => json_encode($requestData),
                "description" => "shopify",
            ]);

            $projectId = current(Hashids::decode($request->project_id));
            $project = $projectModel->find($projectId);

            if (!empty($project)) {
                ProcessShopifyPostbackJob::dispatch($projectId, $requestData)->onQueue("low");

                return response()->json(["message" => "success"]);
            } else {
                Log::warning("Shopify postback - projeto não encontrado");
                //projeto nao existe
                return response()->json(["message" => "project not found"]);
            }
        } catch (\Exception $e) {
            return response()->json(["message" => "error processing postback"]);
        }
    }
}
