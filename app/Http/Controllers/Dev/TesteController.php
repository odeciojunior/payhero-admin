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

class TesteController extends Controller
{
    public function index()
    {

        $inviteEmail = view('core::emails.falta_pouco');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@cludfox.net", "Cloudfox");

        $email->addTo('julioleichtweis@gmail.com', 'Julio');
        $email->setSubject("Falta pouco");
        $email->addContent(
            "text/html", $inviteEmail->render()
        );

        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);

            dd($response);
        } catch (Exception $e) {
            dd($e);
        }

    }
}
