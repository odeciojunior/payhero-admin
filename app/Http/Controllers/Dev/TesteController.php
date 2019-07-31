<?php

namespace App\Http\Controllers\Dev;

use Error;
use Throwable;
use App\Entities\User;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Tag;
use Illuminate\Http\Request;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
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

        $shopify = new ShopifyService('issoeincrivel.myshopify.com', 'cfaa3e8a7aeb7f31e8a5b3b7006645a5');

        dd($shopify->getShopWebhook());

        $shopify->deleteShopWebhook();
 
        $shopify->createShopWebhook([
            "topic"   => "products/create",
            "address" => 'https://app.cloudfox.net/postback/shopify/'.Hashids::encode(92),
            "format"  => "json",
        ]);

        $shopify->createShopWebhook([
            "topic"   => "products/update",
            "address" => 'https://app.cloudfox.net/postback/shopify/'.Hashids::encode(92),
            "format"  => "json",
        ]);

        $shopify->createShopWebhook([
            "topic"   => "orders/updated",
            "address" => 'https://app.cloudfox.net/postback/shopify/'.Hashids::encode(92),
            "format"  => "json",
        ]);

        dd($shopify->getShopWebhook());

        $shopify->setThemeByRole('main');
        $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');
        $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, 'lipoduo.com');

        $shopifyService = new ShopifyService('plotplot.myshopify.com', '8153df9581010e821c22125300fbda56');
        $shopifyService->deleteShopWebhook();
        dd($shopifyService->getShopWebhook());


        $shopifyService->createShopWebhook([
                                               "topic"   => "products/create",
                                               "address" => 'https://bc512aa9.ngrok.io/postback/shopify/dnQ7kZ7wEZ0eJLb',
                                               "format"  => "json",
                                           ]);

        $shopifyService->createShopWebhook([
                                               "topic"   => "products/update",
                                               "address" => 'https://bc512aa9.ngrok.io/postback/shopify/dnQ7kZ7wEZ0eJLb',
                                               "format"  => "json",
                                           ]);

        $shopifyService->createShopWebhook([
                                               "topic"   => "orders/updated",
                                               "address" => 'https://bc512aa9.ngrok.io/postback/shopify/dnQ7kZ7wEZ0eJLb',
                                               "format"  => "json",
                                           ]);

        dd($shopifyService->getShopWebhook());

    }
}


