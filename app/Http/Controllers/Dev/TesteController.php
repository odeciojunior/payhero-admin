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
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\CloudFlareService;
use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;
use Modules\Core\Services\DigitalOceanFileService;

class TesteController extends Controller {


    public function index()
    {

        // $solicitacoes = SiteInvitationRequest::all();

        // foreach($solicitacoes as $solicitacao){
        //     dump($solicitacao->name, $solicitacao->email);
        // }

        $inviteEmail = view('core::emails.falta_pouco',[
            'name' => 'julio'
        ]);

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@cloudfox.net", "Cloudfox");

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
