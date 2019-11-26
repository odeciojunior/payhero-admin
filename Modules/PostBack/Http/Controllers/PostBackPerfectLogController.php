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
            $perfectLogService = new PerfectLogService();

            $tracking = $trackingModel->with(['sale'])
                ->find(current(Hashids::decode($requestValidated['external_reference'])));

            if(isset($tracking)){

                $status = $perfectLogService->parseStatus($requestValidated['status']);

                //ATUALIZAR O STATUS
                $tracking->update([
                    'tracking_code' => $requestValidated['tracking'],
                    'tracking_status_enum' => $status,
                ]);
            }
            return response()->json(['message' => 'Postback received']);
        } catch (\Exception $exception){
            Log::warning('Invalid postback - ' . $exception->getMessage());
            return response()->json(['message' => 'Invalid postback'], 400);
        }
    }
}


