<?php

namespace Modules\PostBack\Http\Controllers;

use App\Jobs\ProcessShopifyPostbackJob;
use App\Jobs\ProcessShopifyTrackingPostbackJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostBackShopifyController extends Controller
{

    public function postBackTracking(Request $request)
    {
        try {
            $requestData = $request->all();
            $projectId = hashids_decode($request->project_id);

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
            $requestData = $request->all();

            $projectId = hashids_decode($request->project_id);
            ProcessShopifyPostbackJob::dispatch($projectId, $requestData)->onQueue("low");

            return response()->json(["message" => "success"]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "error processing postback"]);
        }
    }
}
