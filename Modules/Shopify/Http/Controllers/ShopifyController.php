<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use Modules\Core\Entities\Domain;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Events\ShopifyIntegrationEvent;

/**
 * Class ShopifyController
 * @package Modules\Shopify\Http\Controllers
 */
class ShopifyController extends Controller
{
    /**
     * @return Factory|RedirectResponse|View
     */
    public function index()
    {
        return view('shopify::index');
    }

}

