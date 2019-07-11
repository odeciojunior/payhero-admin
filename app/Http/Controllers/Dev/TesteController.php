<?php

namespace App\Http\Controllers\Dev;

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
        $shopifyService = new ShopifyService('issoeincrivel.myshopify.com','cfaa3e8a7aeb7f31e8a5b3b7006645a5');

        $variant = $shopifyService->getProductVariant('8076898140205');

        $image = $shopifyService->getImage($variant->getProductId(),$variant->getImageId());

        dd($image->getSrc());
    }
}
