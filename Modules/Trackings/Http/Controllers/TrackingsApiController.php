<?php

namespace Modules\Trackings\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\TrackingHistory;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\TrackingService;
use Modules\Trackings\Transformers\TrackingResource;
use Modules\Trackings\Transformers\TrackingShowResource;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
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

    public function resume(Request $request)
    {
        try {
            $trackingService = new TrackingService();

            $data = $request->all();

            $trackings = $trackingService->getResume($data);

            return $trackings;

        } catch (Exception $e) {
            Log::warning('Erro ao exibir códigos de rastreio (TrackingApiController - resume)');
            report($e);

            return response()->json(['message' => 'Erro ao resumo dos rastreamentos'], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $data                 = $request->all();
            $productPlanSaleModel = new ProductPlanSale();
            $saleModel            = new Sale();
            $trackingModel        = new Tracking();
            $productService       = new ProductService();

            if (!empty($data['tracking_code']) && !empty($data['sale_id']) && !empty($data['product_id'])) {
                $saleId    = current(Hashids::connection('sale_id')->decode($data['sale_id']));
                $productId = current(Hashids::decode($data['product_id']));
                if ($saleId && $productId) {
                    $productPlanSale = $productPlanSaleModel->with(['trackings', 'sale.plansSales', 'sale.delivery'])
                                                            ->where([['sale_id', $saleId], ['product_id', $productId]])
                                                            ->first();

                    $tracking = $productPlanSale->trackings->last();

                    //create
                    if(!isset($tracking)){

                        $planSale = $productPlanSale
                            ->sale
                            ->plansSales
                            ->where('plan_id', $productPlanSale->plan_id)
                            ->first();

                        $tracking = $trackingModel->create([
                            'product_plan_sale_id' => $productPlanSale->id,
                            'plans_sale_id' => $planSale->id,
                            'delivery_id' => $productPlanSale->sale->delivery->id,
                            'tracking_code'        => $data['tracking_code'],
                            'tracking_status_enum' => $trackingModel->present()
                                ->getTrackingStatusEnum('posted'),
                        ]);

                        if ($tracking) {

                            //send email
                            $sale = $saleModel->find($saleId);
                            $saleProducts = $productService->getProductsBySale($data['sale_id']);
                            event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));

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

                            //send email
                            $sale = $saleModel->find($saleId);
                            $saleProducts = $productService->getProductsBySale($data['sale_id']);
                            event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));

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
}
