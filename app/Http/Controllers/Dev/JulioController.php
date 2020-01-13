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
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Invitation;
use Modules\Core\Events\SendSmsEvent;
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
use Modules\Core\Services\RemessaOnlineService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

class JulioController extends Controller
{

    public function julioFunction()
    {

        // $dataSms = [
        //     'message'   => 'teste',
        //     'telephone' => '+5555996931098',
        // ];

        // event(new SendSmsEvent($dataSms));

        // $connection = null;
        // $default = 'default';

        // Queue::size();

        //For the delayed jobs
        // var_dump( \Queue::getRedis()->connection($connection)->zrange('queues:'.$default.':delayed' ,0, -1) );

        //For the reserved jobs
        // var_dump( \Queue::getRedis()->connection($connection)->zrange('queues:'.$default.':reserved' ,0, -1) );    }

        $remessaOnlineService = new RemessaOnlineService();

        $quotation = $remessaOnlineService->getCurrentDolarQuotation('euro');

        dd($quotation);
    }
}


