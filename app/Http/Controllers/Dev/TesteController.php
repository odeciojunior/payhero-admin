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
use Illuminate\Support\Facades\Storage;
use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;
use Modules\Core\Services\DigitalOceanFileService;

class TesteController extends Controller
{
    public function index() {

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@cloudfox.net", "Cloudfox");

        $email->setSubject("Parabéns - Compra Aprovada");

        $email->addTo($client->email, $clientNameExploded[0]);
        $email->addContent(
            "text/html", $saleEmail->render()
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            Log::warning('sendgrid não conseguiu enviar email para o cliente na venda ' . $sale->id);
            report($e);
        }

    }
}
