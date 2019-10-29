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
use Modules\Core\Services\ProductService;
use Modules\Trackings\Transformers\TrackingResource;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
    public function index(Request $request){
        try{
            $trackingModel = new Tracking();
            $companyModel = new Company();

            $data = $request->all();

            $userCompanies = $companyModel->where('user_id', auth()->user()->id)
                ->pluck('id')
                ->toArray();

            $trackings = $trackingModel
                ->with([
                        'productPlanSale.sale.transactions',
                        'productPlanSale.product',
                    ])
                ->whereHas('productPlanSale.sale.transactions', function ($query) use ($userCompanies) {
                    $query->whereIn('company_id', $userCompanies);
                })
                ->whereNotNull('tracking_code');

            if(isset($data['status'])){
                $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum($data['status']));
            }

            if(isset($data['tracking_code'])){
                $trackings->where('tracking_code', 'like', '%' . $data['tracking_code'] . '%');
            }

            return TrackingResource::collection($trackings->orderBy('id', 'desc')->paginate(10));

        }catch (Exception $e) {
            Log::warning('Erro ao exibir códigos de rastreio (TrackingApiController - index)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir códigos de rastreio'], 400);
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
                    $productPlanSale = $productPlanSaleModel->with(['trackings'])
                                                            ->where([['sale_id', $saleId], ['product_id', $productId]])
                                                            ->first();

                    $tracking = $productPlanSale->trackings->last();

                    //create
                    if(!isset($tracking)){

                        $tracking = $trackingModel->create([
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
