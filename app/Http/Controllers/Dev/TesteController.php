<?php

namespace App\Http\Controllers\Dev;

use App\Entities\User;
use Modules\Core\Services\ShopifyService;
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

        // $activated = $this->getCloudFlareService()->activationCheck('amparolista.com.br');

        $x = new ShopifyService('toda-bolsa.myshopify.com', '985c9fc4999e55f988a9dfd388fe6890');

        $x->createShopWebhook([
                                        "topic"   => "products/create",
                                        "address" => "https://app.cloudfox.net/postback/shopify/nyOeXZKMagAQap9",
                                        "format"  => "json",
                                    ]);

        $x->createShopWebhook([
                                        "topic"   => "products/update",
                                        "address" => "https://app.cloudfox.net/postback/shopify/nyOeXZKMagAQap9",
                                        "format"  => "json",
                                    ]);

        //$x->setThemeByRole('main');
        //$html = $x->getTemplateHtml('layout/theme.liquid');

        //$x->insertUtmTracking('layout/theme.liquid', $html);
        //$x->deleteShopWebhook();

        $z = $x->getShopWebhook();
        dd($z);

        //$dns = new Dns('goldskin24k.com');

        //dd($dns->getRecords('MX'));

        //dd(dns_get_record("goldskin24k.com", DNS_ANY, $authns, $addtl));
        /*

        */
    }
}
