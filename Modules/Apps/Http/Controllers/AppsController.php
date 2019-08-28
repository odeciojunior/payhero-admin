<?php

namespace Modules\Apps\Http\Controllers;

use App\Entities\HotZappIntegration;
use App\Entities\NotazzIntegration;
use App\Entities\ShopifyIntegration;
use Illuminate\Http\Request;
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

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
        $hotzappIntegrationModel = new HotZappIntegration();
        $shopifyIntegration      = new ShopifyIntegration();
        $notazzIntegration       = new NotazzIntegration();

        $hotzappIngrations   = $hotzappIntegrationModel->where('user_id', auth()->user()->id)->count();
        $shopifyIntegrations = $shopifyIntegration->where('user', auth()->user()->id)->count();
        $notazzIntegrations  = $notazzIntegration->where('user_id', auth()->user()->id)->count();

        return view('apps::index', [
            'hotzappIngrations'   => $hotzappIngrations,
            'shopifyIntegrations' => $shopifyIntegrations,
            'notazzIntegrations'  => $notazzIntegrations,
        ]);
    }
}
