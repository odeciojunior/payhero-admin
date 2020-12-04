<?php


namespace Modules\PostBack\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleReleasedEvent;
use Modules\Core\Services\TrackingmoreService;

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

            $trackingmoreService = new TrackingmoreService();
            $trackingModel = new Tracking();

            $trackingCode = $data['data']['tracking_number'] ?? '';
            $trackingStatus = $data['data']['status'] ?? '';
            $trackingStatus = $trackingmoreService->parseStatus($trackingStatus);

            $trackings = $trackingModel->where('tracking_code', $trackingCode)->get();

            foreach ($trackings as $tracking) {
                $tracking->tracking_status_enum = $trackingStatus;
                if (!in_array($trackingStatus, [
                    $trackingModel->present()->getTrackingStatusEnum('posted'),
                    $trackingModel->present()->getTrackingStatusEnum('exception')
                ])) {
                    $tracking->system_status_enum = $trackingModel->present()->getSystemStatusEnum('valid');
                    $tracking->save();
                    event(new CheckSaleReleasedEvent($tracking->sale_id));
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
