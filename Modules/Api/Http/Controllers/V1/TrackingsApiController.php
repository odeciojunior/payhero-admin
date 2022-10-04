<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Http\Requests\V1\TrackingsApiRequest;
use Modules\Api\Transformers\V1\TrackingsApiResource;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
    public function storeTrackings(Request $request)
    {
        try {
            $requestData = $request->all();

            $verifyRequest = new TrackingsApiRequest();

            $validator = Validator::make(
                $requestData,
                $verifyRequest->storeTrackings(),
                $verifyRequest->messages()
            );

            if ($validator->fails()) {
                return response()->json($validator->errors()->toArray());
            }

            $saleId = current(Hashids::decode($requestData['sale_id']));
            $productId = current(Hashids::decode($requestData['product_id']));
            $trackingCodeId = current(Hashids::decode($requestData['tracking_code']));

            $pps = ProductPlanSale::select("id")
                    ->where("sale_id", $saleId)
                    ->where(function ($query) use ($productId) {
                        $query->where("product_id", $productId)->orWhere("products_sales_api_id", $productId);
                    })
                    ->first();

            $trackingService = new TrackingService();
            $tracking = $trackingService
                    ->createOrUpdateTracking(
                        $trackingCodeId,
                        $pps->id,
                        true
                    );

            return response()->json([
                'message' => 'Código de rastreio salvo',
                'data' => new TrackingsApiResource($tracking)
            ], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                // 'message' => 'Erro ao salvar código de rastreio'
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getTrackings()
    {
        //
    }

    public function showTrackings($id)
    {
        //
    }

    public function updateTrackings($id, Request $request)
    {
        //
    }

    public function deleteTrackings($id)
    {
        //
    }
}
