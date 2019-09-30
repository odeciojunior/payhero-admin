<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\ConvertaxIntegration;

class AppsApiController extends Controller
{
    /**
     * Count of integrations.
     * @return Response
     */
    public function index()
    {
        $hotzappIntegrationModel   = new HotzappIntegration();
        $shopifyIntegration        = new ShopifyIntegration();
        $notazzIntegration         = new NotazzIntegration();
        $convertaxIntegrationModel = new ConvertaxIntegration();

        return response()->json([
            'hotzappIntegrations'   => $hotzappIntegrationModel->where('user_id', auth()->user()->id)->count(),
            'shopifyIntegrations'   => $shopifyIntegration->where('user_id', auth()->user()->id)->count(),
            'notazzIntegrations'    => $notazzIntegration->where('user_id', auth()->user()->id)->count(),
            'convertaxIntegrations' => $convertaxIntegrationModel->where('user_id', auth()->user()->id)->count(),
        ]);
    }

}
