<?php

namespace App\Http\Controllers\Dev;

use App\Entities\User;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\SendgridService;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Tag;
use Illuminate\Http\Request;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Selector\Parser;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use PHPHtmlParser\Selector\Selector;
use Illuminate\Support\Facades\Storage;
use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;
use Modules\Core\Services\DigitalOceanFileService;

class TesteController extends Controller {


    public function index() {

        $sg = new SendgridService();
        dd($sg->getLinkBrand('cloudteste.tk'));
        //$sg->addZone('cloudteste.tk', true);
        //dd($sg->deleteZone('cloudteste.tk'));
        //$sg->setZone('cloudteste.tk');

        dd($sg->teste());


        $cf = new CloudFlareService();
        dd( $this->checkDNS('cloudteste.tk'));
        //dd($cf->addZone('cloudteste.tk'));
        //dd($cf->getZones());

        //dd(Hashids::encode(2));

        dd($cf->zone('cloudteste.tk')->addRecord('A','cloudteste.tk', '1.1.1.1'));

        dd($cf->zone('cloudteste.tk')->getRecords());

        dd( $this->checkDNS('gmail.com.br'));
    }

    protected function checkDNS($host) {

        $variant = INTL_IDNA_VARIANT_2003;
        if ( defined('INTL_IDNA_VARIANT_UTS46') ) {
            $variant = INTL_IDNA_VARIANT_UTS46;
        }
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';

        $Aresult = true;
        $MXresult = checkdnsrr($host, 'MX');

        if (!$MXresult) {
            $this->warnings[NoDNSMXRecord::CODE] = new NoDNSMXRecord();
            $Aresult = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
            if (!$Aresult) {
                $this->error = new NoDNSRecord();
            }
        }
        return $MXresult || $Aresult;
    }

}
