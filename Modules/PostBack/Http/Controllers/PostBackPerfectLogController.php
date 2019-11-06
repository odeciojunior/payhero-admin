<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
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

            Log::debug(json_encode($requestValidated, JSON_PRETTY_PRINT));

            $trackingModel = new Tracking();

            $tracking = $trackingModel->with(['sale'])
                ->find(current(Hashids::decode($requestValidated['external_reference'])));

            if(isset($tracking)){

                $status = 1;

                switch ($requestValidated['status']) {
                    //case 'pending':
                    case 'preparation':
                        $status = $trackingModel->present()->getTrackingStatusEnum('posted');
                        break;
                    case 'sent':
                    case 'resend':
                        $status = $trackingModel->present()->getTrackingStatusEnum('dispatched');
                        break;
                    case 'delivered':
                        $status = $trackingModel->present()->getTrackingStatusEnum('delivered');
                        break;
                    case 'out_for_delivery':
                        $status = $trackingModel->present()->getTrackingStatusEnum('out_for_delivery');
                        break;
                    case 'canceled':
                    case 'erro_fiscal':
                    case 'returned':
                        $status = $trackingModel->present()->getTrackingStatusEnum('exception');
                        break;
                }

                $oldStatus = $tracking->tracking_status_enum;

                //ATUALIZAR O STATUS
                $tracking->update([
                    'tracking_code' => $requestValidated['tracking'],
                    'tracking_status_enum' => $status,
                ]);

                if($oldStatus != $status){
                    //NOTIFICAR O USUARIO
                    $productService = new ProductService();
                    $saleProducts = $productService->getProductsBySale($tracking->sale);
                    event(new TrackingCodeUpdatedEvent($tracking->sale, $tracking, $saleProducts));
                }
            }
            return response()->json(['message' => 'Postback received']);
        } catch (\Exception $exception){
            Log::warning('Invalid postback - ' . $exception->getMessage());
            return response()->json(['message' => 'Invalid postback'], 400);
        }
    }
}


