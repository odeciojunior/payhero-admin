<?php

namespace Modules\Invites\Http\Controllers;

use App\Entities\Company;
use App\Entities\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\EmailHelper;
use Modules\Core\Helpers\StringHelper;
use App\Entities\SiteInvitationRequest;

class InvitesController extends Controller {

    public function index() {

        $invites = Invitation::where('invite',\Auth::user()->id)->get()->toArray();

        foreach($invites as &$invite){

            if($invite['status'] == 'Enviado'){
                $invite['status'] = "<span class='badge badge-info'>Enviado</span>";
            }
            else{
                $invite['status'] = "<span class='badge badge-success'>" . $invite['status'] . "</span>";
            }
        }   
     
        return view('invites::index',[
            'invites' => $invites
        ]);
    }

    public function sendInvitation(Request $request) {

        $requestData = $request->all();

        $requestData['invite'] = \Auth::user()->id;
        $requestData['status'] = "Convite enviado";

        $newParameter = false;

        while(!$newParameter){

            $parameter = StringHelper::randString(15);

            $invite = Invitation::where('parameter', $parameter)->first();

            if($invite == null){
                $newParameter = true;
                $requestData['parameter'] = $parameter;
            }
        }

        $requestData['company'] = Company::where('user', \Auth::user()->id)->first()->id;

        $invite = Invitation::create($requestData);

        EmailHelper::enviarConvite($requestData['email_invited'], $requestData['parameter']);

        return redirect()->route('invites');
    }

    public function getInvitation(Request $request){

        $requestData = $request->all();

        SiteInvitationRequest::create($dados);

        return 'success';
    }
}
