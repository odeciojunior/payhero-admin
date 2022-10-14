<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Http\Requests\V1\TrackingsApiRequest;
use Modules\Api\Transformers\V1\TrackingsApiResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\Api\V1\TrackingsApiService;
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

            $saleId = current(Hashids::connection("sale_id")->decode($requestData['sale_id']));
            $productId = current(Hashids::decode($requestData['product_id']));
            $tracking = $requestData['tracking_code'];

            $productPlanSale = ProductPlanSale::select("id")
                    ->where("sale_id", $saleId)
                    ->where(function ($query) use ($productId) {
                        $query->where("product_id", $productId)->orWhere("products_sales_api_id", $productId);
                    })
                    ->first();

            if (empty($productPlanSale)) {
                return response()->json([
                    'message' => 'Erro ao salvar código de rastreio.'
                ], 400);
            }

            $trackingService = new TrackingService();
            $trackingCreate = $trackingService
                    ->createOrUpdateTracking(
                        $tracking,
                        $productPlanSale->id,
                        true
                    );

            return response()->json([
                'message' => 'Código de rastreio salvo com sucesso.',
                'data' => new TrackingsApiResource($trackingCreate)
            ], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao salvar código de rastreio.'
            ], 400);
        }
    }

    public function getTrackings(Request $request)
    {
        try {
            $requestData = $request->all();

            $trackings = TrackingsApiService::getTrackingsQueryBuilder($requestData);

            return TrackingsApiResource::collection($trackings->simplePaginate(10));
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao listar códigos de rastreio.'
            ], 400);
        }
    }

    public function showTrackings($id)
    {
        try {
            $trackings = TrackingsApiService::showTrackingsQueryBuilder($id);
            $trackings['checkpoints'] = true;

            return new TrackingsApiResource($trackings);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao listar códigos de rastreio.'
            ], 400);
        }
    }

    public function updateTrackings($id, Request $request)
    {
        try {
            $requestData = $request->all();

            $verifyRequest = new TrackingsApiRequest();
            $validator = Validator::make(
                $requestData,
                $verifyRequest->updateTrackings(),
                $verifyRequest->messages()
            );

            if ($validator->fails()) {
                return response()->json($validator->errors()->toArray());
            }

            $trackingService = new TrackingService();
            $trackingUpdate = $trackingService->updateTracking($id, $requestData['tracking_code']);

            if (!empty($trackingUpdate)) {
                return response()->json([
                    'message' => 'Código de rastreio atualizado com sucesso.',
                    'data' => new TrackingsApiResource($trackingUpdate)
                ], 200);
            }

            return response()->json([
                'message' => 'Erro ao atualizar códigos de rastreio.'
            ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao atualizar códigos de rastreio.'
            ], 400);
        }
    }

    public function deleteTrackings($trackingId)
    {
        try {
            $trackingService = new TrackingService();

            $trackingDelete = $trackingService->deleteTracking($trackingId);
            if ($trackingDelete) {
                return response()->json([
                    'message' => 'Código de rastreio excluído com sucesso.'
                ], 200);
            }

            return response()->json([
                'message' => 'Erro ao excluir código de rastreio.'
            ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao excluir código de rastreio.'
            ], 400);
        }
    }
}
