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
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\HotZappIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

class JulioController extends Controller
{

    public function julioFunction()
    {

        //$this->testSms(['message'   => 'teste','telephone' => '5555996931098']);

        // $this->restartShopifyWebhooks();

    }

    public function restartShopifyWebhooks(){

        $webHooksUpdated = 0;

        foreach(ShopifyIntegration::all() as $shopifyIntegration){

            try{
                $shopifyService = new ShopifyService($shopifyIntegration->url_store,$shopifyIntegration->token);

                if(count($shopifyService->getShopWebhook()) != 3){

                    $shopifyService->deleteShopWebhook();

                    $this->createShopWebhook([
                        "topic"   => "products/create",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id),
                        "format"  => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic"   => "products/update",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id),
                        "format"  => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic"   => "orders/updated",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id) . '/tracking',
                        "format"  => "json",
                    ]);

                    $webHooksUpdated++;
                }
            }
            catch(\Exception $e){
                // dump($e);
            }

            dump($webHooksUpdated);
        }
    }

    public function testSms($data){

        event(new SendSmsEvent($dataSms));
    }

}


