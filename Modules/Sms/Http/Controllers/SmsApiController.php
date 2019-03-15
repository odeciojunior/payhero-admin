<?php

namespace Modules\Sms\Http\Controllers;

use App\Plano;
use App\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Sms\Transformers\SmsResource;

class SmsApiController extends Controller {

    public function index() {

        $user_projetos = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']
        ])->get()->toArray();

        $planos_usuario = [];
        foreach($user_projetos as $user_projeto){
            $planos = Plano::where('projeto',$user_projeto['projeto'])->pluck('id')->toArray();
            if(count($planos) > 0){
                foreach($planos as $plano){
                    $planos_usuario[] = $plano;
                }
            }
        }

        $mensagens = \DB::table('mensagens_sms as mensagem')
        ->leftJoin('planos as plano', 'plano.id', 'mensagem.plano')
        ->whereIn('plano.id',$planos_usuario)
        ->orWhere('user', \Auth::user()->id)
        ->select([
            'mensagem.id',
            'mensagem.para',
            'mensagem.mensagem',
            'mensagem.data',
            'mensagem.status',
            'plano.nome as plano',
            'mensagem.evento',
            'mensagem.tipo',
        ])->orderBy('mensagem.id','DESC');

        return SmsResource::collection($mensagens->paginate());
    }

    public function create()
    {
        return view('sms::create');
    }

    public function store(Request $request)
    {
    }

    public function show()
    {
        return view('sms::show');
    }

    public function edit()
    {
        return view('sms::edit');
    }

    public function update(Request $request)
    {
    }

    public function destroy()
    {
    }
}
