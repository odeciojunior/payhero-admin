<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
            'external_reference' => 'nullable',
            'logistic' => 'required',
            'tracking' => 'required',
            'updated_at' => 'required',
            'status' => 'required',
        ]);

        $saleModel = new Sale();

        $sale = $saleModel->with(['productsPlansSale'])
            ->find(current(Hashids::connection('sale_id')->decode($requestValidated['external_reference'])));

//        foreach ($sale->productsPlansSale as $productPlanSale) {
//
//        }
//        event(new TrackingCodeUpdatedEvent($sale));
    }
}


