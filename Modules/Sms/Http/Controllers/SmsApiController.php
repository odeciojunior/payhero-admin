<?php

namespace Modules\Sms\Http\Controllers;

use App\User;
use App\Plano;
use App\MensagemSms;
use App\ZenviaSms;
use App\UserProjeto;
use App\CompraUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Sms\Transformers\SmsResource;
use Modules\Sms\Transformers\SmsProjetosResource;
use Modules\Sms\Transformers\HistoricoSmsResource;

class SmsApiController extends Controller {

    public function atendimentoIndex() {

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

    public function saldo() {

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

        $qtd_sms_disponiveis = \Auth::user()->sms_zenvia_qtd;

        $qtd_sms_enviados = MensagemSms::whereIn('plano',$planos_usuario)->where('tipo','Enviada')->count();

        $qtd_sms_recebidos = MensagemSms::whereIn('plano',$planos_usuario)->where('tipo','Recebida')->count();

        return response()->json([
            'sms_disponiveis' => $qtd_sms_disponiveis,
            'sms_enviados' => $qtd_sms_enviados,
            'sms_recebidos' => $qtd_sms_recebidos
        ]);
    }

    public function historico(){

        $compras = CompraUsuario::where('comprador',\Auth::user()->id)->orderBy('id','DESC');

        return HistoricoSmsResource::collection($compras->paginate());

    }

    public function index(Request $request) {

        $sms = ZenviaSms::where('projeto',$request->id_projeto);

        return SmsProjetosResource::collection($sms->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = $request->id_projeto;

        ZenviaSms::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $sms = ZenviaSms::select('plano','evento','tempo','periodo','status','mensagem')->where('id',$request->id_sms)->first();

        return response()->json($sms);
    }

    public function update(Request $request) {

        $dados = $request->all();

        ZenviaSms::find($dados['id'])->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        ZenviaSms::find($request->id_sms)->delete();

        return response()->json('sucesso');
    }


}
