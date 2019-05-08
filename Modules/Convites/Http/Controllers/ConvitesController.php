<?php

namespace Modules\Convites\Http\Controllers;

use App\Convite;
use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\EmailHelper;
use Modules\Core\Helpers\StringHelper;

class ConvitesController extends Controller {

    public function index() {

        $convites = Convite::where('user_convite',\Auth::user()->id)->get()->toArray();

        foreach($convites as &$convite){

            if($convite['status'] == 'Enviado'){
                $convite['status'] = "<span class='badge badge-info'>Enviado</span>";
            }
            else{
                $convite['status'] = "<span class='badge badge-success'>" . $convite['status'] . "</span>";
            }
        }   
     
        return view('convites::index',[
            'convites' => $convites
        ]);
    }

    public function enviarConvite(Request $request) {

        $dados = $request->all();

        $dados['user_convite'] = \Auth::user()->id;
        $dados['status'] = "Convite enviado";

        $novoParametro = false;

        while(!$novoParametro){

            $parametro = StringHelper::randString(15);

            $convite = Convite::where('parametro', $parametro)->first();

            if($convite == null){
                $novoParametro = true;
                $dados['parametro'] = $parametro;
            }
        }

        $dados['empresa'] = @Empresa::where('user', \Auth::user()->id)->first()->id;

        $convite = Convite::create($dados);

        EmailHelper::enviarConvite($dados['email_convidado'], $dados['parametro']);

        return redirect()->route('convites');
    }

}
