<?php

namespace Modules\Trackings\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\TrackingHistory;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\PerfectLogService;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data                 = $request->all();
            $productPlanSaleModel = new ProductPlanSale();
            $saleModel            = new Sale();
            $productService       = new ProductService();

            if (!empty($data['tracking_code']) && !empty($data['sale_id']) && !empty($data['product_id'])) {
                $saleId    = current(Hashids::connection('sale_id')->decode($data['sale_id']));
                $productId = current(Hashids::decode($data['product_id']));
                if ($saleId && $productId) {
                    $productPlanSale = $productPlanSaleModel->where([['sale_id', $saleId], ['product_id', $productId]])
                                                            ->first();
                    //create
                    if ($productPlanSale && empty($productPlanSale->tracking_code)) {
                        $trackingCodeupdated = $productPlanSale->update([
                                                                            'tracking_code'        => $data['tracking_code'],
                                                                            'tracking_status_enum' => $productPlanSaleModel->present()
                                                                                                                           ->getTrackingStatusEnum('posted'),
                                                                        ]);
                        if ($trackingCodeupdated) {

                            //send email
                            //$sale = $saleModel->find($saleId);
                            //$saleProducts = $productService->getProductsBySale($data['sale_id']);
                            //event(new TrackingCodeUpdatedEvent($sale, $productPlanSale, $saleProducts));

                            return response()->json([
                                                        'message' => 'Código de rastreio salvo',
                                                        'data'    => [
                                                            'tracking_code'   => $productPlanSale->tracking_code,
                                                            'tracking_status' => Lang::get('definitions.enum.product_plan_sale.tracking_status_enum.' . $productPlanSaleModel->present()
                                                                                                                                                                             ->getTrackingStatusEnum($productPlanSale->tracking_status_enum)),
                                                        ],
                                                    ], 200);
                        } else {
                            return response()->json([
                                                        'message' => 'Erro ao salvar código de rastreio',
                                                    ], 400);
                        }
                    //update
                    } else if ($productPlanSale && $productPlanSale->tracking_code != $data['tracking_code']) {
                        $trackingCode = $productPlanSale->tracking_code;

                        $trackingCodeupdated = $productPlanSale->update([
                                                                            'tracking_code' => $data['tracking_code'],
                                                                        ]);
                        if ($trackingCodeupdated) {
                            $trackingHistoryModel = new TrackingHistory();
                            $trackingHistoryModel->create([
                                                              'product_plan_sale_id' => $productPlanSale->id,
                                                              'tracking_code'        => $trackingCode,
                                                              'tracking_type_enum'   => null,
                                                              'tracking_status_enum' => null,
                                                              'tracking_date'        => null,
                                                              'description'          => null,
                                                          ]);

                            //send email
                            //$sale = $saleModel->find($saleId);
                            //$saleProducts = $productService->getProductsBySale($data['sale_id']);
                            //event(new TrackingCodeUpdatedEvent($sale, $productPlanSale, $saleProducts));

                            return response()->json([
                                                        'message' => 'Código de rastreio alterado',
                                                        'data'    => [
                                                            'tracking_code'   => $productPlanSale->tracking_code,
                                                            'tracking_status' => Lang::get('definitions.enum.product_plan_sale.tracking_status_enum.' . $productPlanSaleModel->present()
                                                                                                                                                                             ->getTrackingStatusEnum($productPlanSale->tracking_status_enum)),
                                                        ],
                                                    ], 200);
                        }
                    }

                    $perfectLogService = new PerfectLogService();
                    $perfectLogService->track(Hashids::encode($productPlanSale->id), $data['tracking_code']);
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
