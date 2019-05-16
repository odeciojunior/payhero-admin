<?php

namespace Modules\Invites\Http\Controllers;

use App\Empresa;
use App\Convite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Invites\Transformers\InvitesResource;


class InvitesApiController extends Controller {

    public function convites() {

        $convites = Convite::where('user_convite',\Auth::user()->id);

        return ConvitesResource::collection($convites->paginate());
    }

    public function enviarConvite(Request $request) {

        $dados = $request->all();

        $dados['user_convite'] = \Auth::user()->id;
        $dados['status'] = "Convite enviado";
        $dados['parametro']  = $this->randString(15);

        $dados['empresa'] = @Empresa::where('user', \Auth::user()->id)->first()->id;

        try{
            $convite = Convite::create($dados);

            Mail::send('convites::email_convite', [ 'convite' => $convite ], function ($mail) use ($dados) {
                $mail->from('julioleichtweis@gmail.com', 'Cloudfox');

                $mail->to($dados['email_convidado'], 'Cloudfox')->subject('Convite!');
            });
        }
        catch(\Exception $e){

        }

        return redirect()->route('convites');
    }

    function randString($size){

        $novo_parametro = false;

        while(!$novo_parametro){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parametro = "";

            for($count= 0; $size > $count; $count++){
                $parametro.= $basic[rand(0, strlen($basic) - 1)];
            }

            $convite = Convite::where('parametro', $parametro)->first();

            if($convite == null){
                $novo_parametro = true;
            }

        }

        return $parametro;
    }

}
