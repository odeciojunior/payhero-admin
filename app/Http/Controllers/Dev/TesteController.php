<?php

namespace App\Http\Controllers\Dev;

use Error;
use Exception;
use Throwable;
use DOMDocument;
use App\Entities\Plan;
use App\Entities\User;
use PHPHtmlParser\Dom;
use App\Entities\Domain;
use PHPHtmlParser\Dom\Tag;
use Illuminate\Http\Request;
use App\Entities\DomainRecord;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use Illuminate\Support\Facades\DB;
use PHPHtmlParser\Selector\Parser;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use PHPHtmlParser\Selector\Selector;
use App\Entities\SiteInvitationRequest;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\CloudFlareService;
use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;
use Modules\Core\Services\DigitalOceanFileService;

class TesteController extends Controller
{

    public function index()
    {

/*        $plans = Plan::whereNotNull('shopify_variant_id')->get();

        foreach($plans as $plan){

            $product = $plan->products->first();
            
            if(!empty($product)){
                $product->update([
                    'shopify_id'         => $plan->shopify_id,
                    'shopify_variant_id' => $plan->shopify_variant_id,
                ]); 

            }
        }*/


        $shopifyService = new ShopifyService('toda-bolsa.myshopify.com','985c9fc4999e55f988a9dfd388fe6890');

        dd($shopifyService->getShopWebhook());

    }
}


