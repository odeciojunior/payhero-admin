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



        $DominioParaVerificar = "http://http://www.uol.com";
        $x=parse_url($DominioParaVerificar);
        if (filter_var($DominioParaVerificar, FILTER_VALIDATE_URL))
        {
            echo"$DominioParaVerificar é valido";
        }
        //$shopify = new ShopifyService('plotplot.myshopify.com', '8153df9581010e821c22125300fbda56');
        //dd($shopify->getShopWebhook());

//        $shopify->setThemeByRole('main');
//        $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');
//        $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, 'lipoduo.com');

        //$do = app(DigitalOceanFileService::class);
        //dd($do->getTemporaryUrlFile('/uploads/user/5n4KovG1YGyDEmO/private/documents/6PQ4eog8VzSCeuzFbQmgZ5rUGumOdVpm1plhBF0o.pdf',120));
/*
        $shopifyService = new ShopifyService('plotplot.myshopify.com', '8153df9581010e821c22125300fbda56');
        $shopifyService->deleteShopWebhook();
        //dd($shopifyService->getShopWebhook());


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

        */
    }
}


