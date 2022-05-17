<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Shipping\Transformers\ShippingResource;
use Modules\Shipping\Http\Controllers\ShippingApiController;

class ShippingApiDemoController extends ShippingApiController
{
    public function index($projectId)
    {
        try {

            if (empty($projectId)) {
                return response()->json([
                    'message' => 'Erro ao listar dados de frete',
                ], 400);
            }

            $shippings = Shipping::where('project_id', current(Hashids::decode($projectId)));

            return ShippingResource::collection($shippings->orderBy('id', 'DESC')->paginate(5));
                
        } catch (Exception $e) {
            
            report($e);

            return response()->json([
                'message' => 'Erro ao listar dados de frete',
            ], 400);
        }
    }
}
