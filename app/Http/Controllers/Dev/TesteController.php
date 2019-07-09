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
    public function index()
    {
        $x = new ShopifyService('gercastore.myshopify.com', 'bb78f036e257b07b8cc535a54e82d777');
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
