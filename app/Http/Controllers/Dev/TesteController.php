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
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var SendgridService
     */
    private $sendgridService;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|CloudFlareService
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    public function index()
    {

        $shopifyService = new ShopifyService('plotplot.myshopify.com', '8153df9581010e821c22125300fbda56');

        dd($shopifyService->getShopWebhook());

        try {
            $x = Domain::first();
        } catch (\Exception $e) {
            dd($e);
        } catch (Error $e) {
            // This should work
            dd($e);
        } catch (Throwable $e) {
            // This should work as well
            dd('c');
        }

        //        $shopifyService = new ShopifyService('toda-bolsa.myshopify.com','985c9fc4999e55f988a9dfd388fe6890');
        //
        //        $shopifyService->deleteShopWebhook();
        //
        //        $shopifyService->createShopWebhook([
        //                                               "topic"   => "products/create",
        //                                               "address" => "https://d1a7e345.ngrok.io/postback/shopify/7DPXw3X0B3zmpqx",
        //                                               "format"  => "json",
        //                                           ]);
        //
        //        $shopifyService->createShopWebhook([
        //                                               "topic"   => "products/update",
        //                                               "address" => "https://d1a7e345.ngrok.io/postback/shopify/7DPXw3X0B3zmpqx",
        //                                               "format"  => "json",
        //                                           ]);
        //
        //        dd($shopifyService->getShopWebhook());

    }
}
