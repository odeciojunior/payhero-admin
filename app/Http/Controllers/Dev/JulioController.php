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
use Modules\Sales\Exports\Reports\Report;
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
        //     'telephone' => '5555996931098',
        // ];

        // event(new SendSmsEvent($dataSms));

        // dd("foi");

        // $connection = null;
        // $default = 'default';

        // Queue::size();

        //For the delayed jobs
        // var_dump( \Queue::getRedis()->connection($connection)->zrange('queues:'.$default.':delayed' ,0, -1) );

        //For the reserved jobs
        // var_dump( \Queue::getRedis()->connection($connection)->zrange('queues:'.$default.':reserved' ,0, -1) );    }

        // $remessaOnlineService = new RemessaOnlineService();

        // $quotation = $remessaOnlineService->getCurrentDolarQuotation('eurofd');

        // dd($quotation);

        // SELECT sale_id, fantasy_name, value, transactions.created_at FROM `transactions` JOIN companies WHERE sale_id IN ('184340','168175','173651','182919','235479','50397','127525','89524','165410','96095','209409','155379','211457','166421', '38161','168686','172741','132421','179943','110939','154692','159452','106670','86760','197990','67294','239153','180017', '176561','181595','105822','160265','114464','60007','76954','154251','159343','129864','180227','156828','27861','117638') and company_id = companies.id and invitation_id is null and company_id is not null ORDER BY sale_id

        (new Report(User::find(24)))->queue('arquivo.xls');

    }
}


