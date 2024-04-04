<?php

namespace Modules\Api\Http\Controllers\V1;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Http\Requests\V1\TrackingsApiRequest;
use Modules\Api\Transformers\V1\TrackingsApiResource;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
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

            if (!empty($requestData["tracking_code"]) && !empty($requestData["sale_id"]) && empty($requestData["product_id"])) {
                $saleId = current(Hashids::connection("sale_id")->decode($requestData['sale_id']));

                $tracking = $requestData['tracking_code'];

                $productPlanSales = ProductPlanSale::select("id")->where("sale_id", $saleId)->get();
                $notify = false;

                $trackingService = new TrackingService();

                foreach ($productPlanSales as $productPlanSale)
                {
                    $trackingCreate = $trackingService->createOrUpdateTracking($tracking, $productPlanSale->id, $saleId, true, false);
                    if ($trackingCreate) {
                        $notify = true;
                    }
                }
                
                if ($notify) {
                    event(new TrackingCodeUpdatedEvent($trackingCreate->id));
                    
                    return response()->json([
                        'message' => 'Código de rastreio salvo com sucesso.',
                        'data' => new TrackingsApiResource($trackingCreate)
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Erro ao salvar código de rastreio.'
                    ], 400);
                }
            }

            if (!empty($requestData["tracking_code"]) && !empty($requestData["sale_id"]) && !empty($requestData["product_id"])) {
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
            }
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

            $trackingService = new TrackingsApiService();
            $trackings = $trackingService->getTrackingsQueryBuilder($requestData);

            return TrackingsApiResource::collection($trackings->simplePaginate(10));
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function showTrackings($id)
    {
        try {
            $trackingsApiService = new TrackingsApiService();
            $trackingService = new TrackingService();
            $trackingModel = new Tracking();

            $trackingBuilder = $trackingsApiService->getTrackingsQueryBuilder([], $id);

            $trackings = $trackingBuilder->first();

            if (empty($trackings)) {
                return response()->json([
                    'message' => 'Código de rastreio não encontrado.'
                ], 404);
            }

            $postedStatus = $trackingModel->present()->getTrackingStatusEnum("posted");
            $checkpoints = collect();

            //objeto postado
            $checkpoints->add([
                "tracking_status_enum" => $postedStatus,
                "tracking_status" => __(
                    "definitions.enum.tracking.tracking_status_enum." . $trackingModel->present()->getTrackingStatusEnum($postedStatus)
                ),
                "created_at" => Carbon::parse($trackings->created_at)->format("d/m/Y"),
                "event" => "Objeto postado. As informações de rastreio serão atualizadas nos próximos dias.",
            ]);

            $apiTracking = $trackingService->findTrackingApi($trackings);
            $checkpointsApi = $trackingService->getCheckpointsApi($trackings, $apiTracking);

            $checkpoints = $checkpoints
                ->merge($checkpointsApi)
                ->unique()
                ->sortKeysDesc()
                ->values()
                ->toArray();

            $trackings->checkpoints = $checkpoints;



            return new TrackingsApiResource($trackings);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                // 'message' => 'Erro ao listar código de rastreio.'
                'message' => $e->getMessage()
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
