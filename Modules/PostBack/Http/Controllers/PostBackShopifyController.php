<?php

namespace Modules\PostBack\Http\Controllers;

use App\Jobs\ProcessShopifyPostbackJob;
use App\Jobs\ProcessShopifyTrackingPostbackJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postBackTracking(Request $request)
    {
        try {
            $requestData = $request->all();

            PostbackLog::create([
                "origin" => 5,
                "data" => json_encode($requestData),
                "description" => "shopify-tracking",
            ]);

            $projectId = current(Hashids::decode($request->project_id));
            $project = DB::table('projects')->select('id')->where('id',$projectId)->exists();

            if (empty($project)) {
                Log::warning("Shopify atualizar código de rastreio - projeto não encontrado");
                //projeto nao existe
                return response()->json(["message" => "project not found"]);
            }

            ProcessShopifyTrackingPostbackJob::dispatch($projectId, $requestData)->onQueue(
                "postback-shopify-tracking"
            );

            return response()->json(["message" => "success"]);

        } catch (\Exception $e) {
            return response()->json(["message" => "error processing postback"]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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
