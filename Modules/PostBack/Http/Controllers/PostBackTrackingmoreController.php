<?php

namespace Modules\PostBack\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTrackingmorePostbackJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\ProductPlanSale;

class PostBackTrackingmoreController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @see https://www.trackingmore.com/webhook.html
     * @see https://www.trackingmore.com/api-logistics_status.html
     */
    public function postBackListener(Request $request)
    {
        try {
            $data = $request->all();

            $postBackLogModel = new PostbackLog();

            $postBackLogModel->create([
                "origin" => 7,
                "data" => json_encode($data),
                "description" => "trackingmore",
            ]);

            $trackingCode = $data["data"]["tracking_number"] ?? "";

            if ($trackingCode) {
                ProcessTrackingmorePostbackJob::dispatch($trackingCode);
            }

            return response()->json(["message" => "Postback received!"]);
        } catch (\Exception $ex) {
            report($ex);
            Log::info($ex->getMessage());
            return response()->json(["message" => "Postback listerner error"]);
        }
    }
}
