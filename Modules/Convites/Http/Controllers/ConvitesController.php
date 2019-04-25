<?php

namespace Modules\Convites\Http\Controllers;

use App\Convite;
use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Helpers\EmailHelper;

class ConvitesController extends Controller {

    public function index() {

        $convites = Convite::where('user_convite',\Auth::user()->id)->get()->toArray();

        return view('convites::index',[
            'convites' => $convites
        ]);
    }

    public function enviarConvite(Request $request) {

        $dados = $request->all();

        $dados['user_convite'] = \Auth::user()->id;
        $dados['status'] = "Convite enviado";
        $dados['parametro']  = $this->randString(15);

        $dados['empresa'] = @Empresa::where('user', \Auth::user()->id)->first()->id;

        $convite = Convite::create($dados);

        EmailHelper::enviarConvite($dados['email_convidado'], $dados['parametro']);

        return redirect()->route('convites');
    }

    function randString($size){

        $novoParametro = false;

        while(!$novoParametro){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parametro = "";

            for($count= 0; $size > $count; $count++){
                $parametro.= $basic[rand(0, strlen($basic) - 1)];
            }

            $convite = Convite::where('parametro', $parametro)->first();

            if($convite == null){
                $novoParametro = true;
            }

        }

        return $parametro;
    }

}
