<?php


namespace Modules\PostBack\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\AftershipService;

/**
 * Class PostBackAftershipController
 * @package App\Http\Controllers\WebService\Aftership
 */
class PostBackAftershipController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @see https://docs.aftership.com/api/4/webhook
     * @see https://docs.aftership.com/api/4/delivery-status
     */
    public function postBackListener(Request $request)
    {
        try {
            $data = $request->all();

            $aftershipService = new AftershipService();
            $trackingModel = new Tracking();

            $trackingCode = $data['msg']['tracking_number'] ?? '';
            $trackingStatus =  $data['msg']['tag'] ?? '';
            $trackingStatus = $aftershipService->parseStatus($trackingStatus);

            $trackings = $trackingModel->where('tracking_code', $trackingCode)->get();

            foreach ($trackings as $tracking){
                $tracking->tracking_status_enum = $trackingStatus;
                $tracking->save();
            }

            return response()->json(['message' => 'Postback received!']);

        } catch (\Exception $ex) {
            report($ex);
            Log:info($ex->getMessage());
            return response()->json(['message' => 'Postback listerner error']);
        }
    }
}
