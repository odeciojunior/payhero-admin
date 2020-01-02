<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\PerfectLogService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PostBackShopifyController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackPerfectLogController extends Controller
{
    public function postBackListener(Request $request)
    {
        try{
            $requestValidated = $request->validate([
                'code' => 'required',
                'external_reference' => 'required',
                'logistic' => 'required',
                'tracking' => 'required',
                'updated_at' => 'required',
                'status' => 'required',
            ]);

            $trackingModel = new Tracking();
            $perfectlogService = new PerfectLogService();

            $trackingStatus = $perfectlogService->parseStatus($requestValidated['status']);

            $trackings = $trackingModel->where('tracking_code', $requestValidated['tracking'])->get();

            foreach ($trackings as $tracking){
                $tracking->tracking_status_enum = $trackingStatus;
                $tracking->save();
            }

            return response()->json(['message' => 'Postback received']);
        } catch (\Exception $exception){
            report($exception);
            Log::warning('Invalid postback - ' . $exception->getMessage());
            return response()->json(['message' => 'Invalid postback'], 400);
        }
    }
}


