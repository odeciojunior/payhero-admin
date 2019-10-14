<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
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

        $productPlanSaleModel = new ProductPlanSale();

        $productPlanSale = $productPlanSaleModel->find(current(Hashids::decode($requestValidated['external_reference'])));

        if(isset($productPlanSale)){

            $status = 0;

            switch ($requestValidated['status']){
                case 'sent':
                case 'out_for_delivery':
                case 'resend':
                    $status = $productPlanSale->present()->getStatusEnum('dispatched');
                    break;
                case 'delivered':
                    $status = $productPlanSale->present()->getStatusEnum('delivered');
                    break;
                //case 'preparation':
                //case 'canceled':
                //case 'pending':
                //case 'erro_fiscal':
                //case 'returned':
                default:
                    $status = $productPlanSale->present()->getStatusEnum('posted');
                    break;
            }

            //ATUALIZAR O STATUS

            //NOTIFICAR O USUARIO

            //event(new TrackingCodeUpdatedEvent($sale));
        }
    }
}


