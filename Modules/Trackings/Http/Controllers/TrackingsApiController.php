<?php

namespace Modules\Trackings\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\TrackingHistory;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Events\TrackingsImportedEvent;
use Modules\Core\Imports\TrackingsImport;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\PerfectLogService;
use Modules\Core\Services\TrackingService;
use Modules\Trackings\Http\Requests\TrackingStoreRequest;
use Modules\Trackings\Transformers\TrackingResource;
use Modules\Trackings\Transformers\TrackingShowResource;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $trackingService = new TrackingService();

            $data = $request->all();

            $trackings = $trackingService->getTrackings($data);

            return TrackingResource::collection($trackings);

        } catch (Exception $e) {
            Log::warning('Erro ao exibir códigos de rastreio (TrackingApiController - index)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir códigos de rastreio'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|TrackingShowResource
     */
    public function show($id)
    {
        try {
            $trackingModel = new Tracking();

            $trackingId = current(Hashids::decode($id));

            $tracking = $trackingModel->with([
                'product',
                'delivery',
                'history'
            ])->find($trackingId);

            return new TrackingShowResource($tracking);

        } catch (Exception $e) {
            Log::warning('Erro ao exibir detalhes do código de rastreio (TrackingApiController - show)');
            report($e);
            return response()->json(['message' => 'Erro ao exibir detalhes do código de rastreio'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resume(Request $request)
    {
        try {
            $trackingService = new TrackingService();

            $data = $request->all();

            $trackings = $trackingService->getTrackings($data, true);

            return $trackings;

        } catch (Exception $e) {
            Log::warning('Erro ao exibir códigos de rastreio (TrackingApiController - resume)');
            report($e);

            return response()->json(['message' => 'Erro ao resumo dos rastreamentos'], 400);
        }
    }

    /**
     * @param TrackingStoreRequest $request
     * @return JsonResponse
     */
    public function store(TrackingStoreRequest $request)
    {
        try {
            $data = $request->all();
            $productPlanSaleModel = new ProductPlanSale();
            $trackingModel = new Tracking();
            $trackingService = new TrackingService();

            if (!empty($data['tracking_code']) && !empty($data['sale_id']) && !empty($data['product_id'])) {
                $saleId    = current(Hashids::connection('sale_id')->decode($data['sale_id']));
                $productId = current(Hashids::decode($data['product_id']));
                if ($saleId && $productId) {
                    $productPlanSale = $productPlanSaleModel->with(['tracking', 'sale.plansSales.plan.productsPlans', 'sale.delivery'])
                                                            ->where([['sale_id', $saleId], ['product_id', $productId]])
                                                            ->first();

                    $tracking = $productPlanSale->tracking;

                    //create
                    if(!isset($tracking)){

                        $tracking = $trackingService->createTracking($data['tracking_code'], $productPlanSale);

                        if ($tracking) {

                            $perfectLogService = new PerfectLogService();
                            $perfectLogService->track(Hashids::encode($tracking->id), $data['tracking_code']);

                            return response()->json([
                                                        'message' => 'Código de rastreio salvo',
                                                        'data'    => [
                                                            'tracking_code'   => $tracking->tracking_code,
                                                            'tracking_status' => Lang::get('definitions.enum.tracking.tracking_status_enum.' . $trackingModel->present()
                                                                                                                                                                             ->getTrackingStatusEnum($tracking->tracking_status_enum)),
                                                        ],
                                                    ], 200);
                        } else {
                            return response()->json([
                                                        'message' => 'Erro ao salvar código de rastreio',
                                                    ], 400);
                        }
                    //update
                    } else {
                        $trackingStatus = $tracking->tracking_status_enum;

                        $trackingCodeupdated = $tracking->update([
                                                                    'tracking_code' => $data['tracking_code'],
                                                                 ]);
                        if ($trackingCodeupdated) {
                            $trackingHistoryModel = new TrackingHistory();

                            $trackingHistoryModel->firstOrNew([
                                                              'tracking_id' => $tracking->id,
                                                              'tracking_status_enum' => $trackingStatus,
                                                          ]);

                            $perfectLogService = new PerfectLogService();
                            $perfectLogService->track(Hashids::encode($tracking->id), $data['tracking_code']);

                            return response()->json([
                                                        'message' => 'Código de rastreio alterado',
                                                        'data'    => [
                                                            'tracking_code'   => $tracking->tracking_code,
                                                            'tracking_status' => Lang::get('definitions.enum.tracking.tracking_status_enum.' . $trackingModel->present()
                                                                                                                                                                             ->getTrackingStatusEnum($tracking->tracking_status_enum)),
                                                        ],
                                                    ], 200);
                        }
                    }
                }
            } else {
                return response()->json([
                                            'message' => 'Erro ao salvar código de rastreio',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar alterar código de rastreio (TrackingApiController - store)');
            report($e);

            return response()->json(['message' => 'Erro ao salvar código de rastreio'], 400);
        }
        return response()->json([], 200);
    }

    /**
     * @param $trackingId
     * @return JsonResponse
     */
    public function notifyClient($trackingId)
    {
        try {
            $trackingModel = new Tracking();
            $productService = new ProductService();

            if (isset($trackingId)) {

                $tracking = $trackingModel->with('sale')
                    ->find(current(Hashids::decode($trackingId)));

                if ($tracking && $tracking->sale) {

                    $saleProducts = $productService->getProductsBySale($tracking->sale);
                    event(new TrackingCodeUpdatedEvent($tracking->sale, $tracking, $saleProducts));

                    return response()->json(['message' => 'Notificação enviada com sucesso']);
                } else {
                    return response()->json(['message' => 'Erro ao notificar cliente'], 400);
                }
            } else {
                return response()->json(['message' => 'Erro ao notificar cliente'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao notificar cliente (TrackingApiController - notifyClient)');
            report($e);

            return response()->json(['message' => 'Erro ao notificar cliente'], 400);
        }
    }

    public function import(Request $request){

        if($request->hasFile('import_xls')){
            $user = auth()->user();
            Excel::queueImport(new TrackingsImport($user->account_owner_id), request()->file('import_xls'))->chain([
                new TrackingsImportedEvent($user)
            ]);
        }
        return 'nop';

    }
}
