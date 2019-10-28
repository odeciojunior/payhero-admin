<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PostBackShopifyController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackPerfectLogController extends Controller
{
    public function postBackListener(Request $request)
    {
        $requestValidated = $request->validate([
            'code' => 'required',
            'external_reference' => 'required',
            'logistic' => 'required',
            'tracking' => 'required',
            'updated_at' => 'required',
            'status' => 'required',
        ]);

        Log::debug(json_encode($requestValidated, JSON_PRETTY_PRINT));

        $productPlanSaleModel = new ProductPlanSale();

        $productPlanSale = $productPlanSaleModel->with(['sale'])
            ->find(current(Hashids::decode($requestValidated['external_reference'])));

        if(isset($productPlanSale)){

            $status = 0;

            switch ($requestValidated['status']) {
                //case 'preparation':
                case 'sent':
                case 'resend':
                    $status = $productPlanSaleModel->present()->getTrackingStatusEnum('dispatched');
                    break;
                case 'delivered':
                    $status = $productPlanSaleModel->present()->getTrackingStatusEnum('delivered');
                    break;
                case 'out_for_delivery':
                    $status = $productPlanSaleModel->present()->getTrackingStatusEnum('out_for_delivery');
                    break;
                case 'canceled':
                case 'pending':
                case 'erro_fiscal':
                case 'returned':
                    $status = $productPlanSaleModel->present()->getTrackingStatusEnum('exception');
                    break;
            }

            //ATUALIZAR O STATUS
            $productPlanSale->update([
                'tracking_code' => $requestValidated['tracking'],
                'tracking_status_enum' => $status,
            ]);

            //NOTIFICAR O USUARIO
            $productService = new ProductService();
            $saleProducts = $productService->getProductsBySale(Hashids::connection('sale_id')->encode($productPlanSale->sale->id));
            event(new TrackingCodeUpdatedEvent($productPlanSale->sale, $productPlanSale, $saleProducts));

        }

        return response()->json(['message' => 'ok']);
    }
}


