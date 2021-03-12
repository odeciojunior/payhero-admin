<?php


namespace Modules\PostBack\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;
use Modules\Core\Services\TrackingmoreService;
use Modules\Core\Services\TrackingService;

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

            $trackingService = new TrackingService();
            $trackingmoreService = new TrackingmoreService();
            $trackingModel = new Tracking();

            $trackingCode = $data['data']['tracking_number'] ?? '';
            $trackingStatus = $trackingmoreService->parseStatus($data['data']['status'] ?? '');

            $trackings = $trackingModel->with('productPlanSale')
                ->where('tracking_code', $trackingCode)
                ->get();

            foreach ($trackings as $tracking) {
                if ($tracking->tracking_status_enum != $trackingStatus) {
                    $trackingService->createOrUpdateTracking($trackingCode, $tracking->productPlanSale, false, false);
                }
            }

            return response()->json(['message' => 'Postback received!']);

        } catch (\Exception $ex) {
            report($ex);
            Log:
            info($ex->getMessage());
            return response()->json(['message' => 'Postback listerner error']);
        }
    }
}
