<?php

namespace App\Http\Controllers\Dev;

use Exception;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Checkout\Classes\MP;
use Modules\Core\Entities\Pixel;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\DomainRecord;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Services\NotazzService;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\HotZappIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

class JulioController extends Controller
{

    public function julioFunction()
    {

        $sales = Sale::whereNull('gateway_id')->where('status', '!=', 10)->limit( 10)->orderBy('id', 'desc')->count();

        dd($sales);
    }


}


