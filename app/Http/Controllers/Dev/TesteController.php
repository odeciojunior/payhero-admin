<?php

namespace App\Http\Controllers\Dev;

use App\Entities\User;
use Error;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Tag;
use Illuminate\Http\Request;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Selector\Parser;
use Throwable;
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

        $shopifyService = new ShopifyService('plotplot.myshopify.com', '8153df9581010e821c22125300fbda56');
        dd($shopifyService->getShopWebhook());
        $shopifyService->deleteShopWebhook();

        $shopifyService->createShopWebhook([
                                               "topic"   => "products/create",
                                               "address" => 'https://ef413380.ngrok.io/postback/shopify/dnQ7kZ7wEZ0eJLb',
                                               "format"  => "json",
                                           ]);

        $shopifyService->createShopWebhook([
                                               "topic"   => "products/update",
                                               "address" => 'https://ef413380.ngrok.io/postback/shopify/dnQ7kZ7wEZ0eJLb',
                                               "format"  => "json",
                                           ]);

//        $shopifyService->createShopWebhook([
//                                               "topic"   => "orders/update",
//                                               "address" => 'https://ef413380.ngrok.io/postback/shopify/dnQ7kZ7wEZ0eJLb',
//                                               "format"  => "json",
//                                           ]);

        dd($shopifyService->getShopWebhook());
    }
}


