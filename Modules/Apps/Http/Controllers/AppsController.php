<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\HotzappIntegration;

/**
 * Class AppsController
 * @package Modules\Apps\Http\Controllers
 */
class AppsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $hotzappIntegrationModel   = new HotzappIntegration();
        $shopifyIntegration        = new ShopifyIntegration();
        $notazzIntegration         = new NotazzIntegration();
        $convertaxIntegrationModel = new ConvertaxIntegration();

        $hotzappIngrations     = $hotzappIntegrationModel->where('user_id', auth()->user()->id)->count();
        $shopifyIntegrations   = $shopifyIntegration->where('user_id', auth()->user()->id)->count();
        $notazzIntegrations    = $notazzIntegration->where('user_id', auth()->user()->id)->count();
        $convertaxIntegrations = $convertaxIntegrationModel->where('user_id', auth()->user()->id)->count();

        return view('apps::index', [
            'hotzappIngrations'     => $hotzappIngrations,
            'shopifyIntegrations'   => $shopifyIntegrations,
            'notazzIntegrations'    => $notazzIntegrations,
            'convertaxIntegrations' => $convertaxIntegrations,
        ]);
    }
}
