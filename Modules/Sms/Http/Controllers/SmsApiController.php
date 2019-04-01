<?php

namespace Modules\Sms\Http\Controllers;

use App\User;
use App\Plano;
use App\Projeto;
use DateTimeZone;
use App\ZenviaSms;
use App\MensagemSms;
use App\UserProjeto;
use Zenvia\Model\Sms;
use App\CompraUsuario;
use Zenvia\Model\SmsFacade;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
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

    public function enviarMensagem(Request $request){

        $user = \Auth::user();

        if($user->sms_zenvia_qtd > 0){
            $dados = $request->all();

            $smsFacade = new SmsFacade('healthlab.corp','hLQNVb7VQk');
            $sms = new Sms();
            $sms->setTo('55'.preg_replace("/[^0-9]/", "", $dados['telefone']));
            $sms->setMsg($dados['mensagem']);
            $id_sms = uniqid();
            $sms->setId($id_sms);
            $sms->setCallbackOption(Sms::CALLBACK_NONE);
            $date = new \DateTime();
            $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
            $schedule = $date->format("Y-m-d\TH:i:s");
            $sms->setSchedule($schedule);

            try{
                $response = $smsFacade->send($sms);

                MensagemSms::create([
                    'id_zenvia' => $id_sms,
                    'para' => '55'.preg_replace("/[^0-9]/", "", $dados['telefone']),
                    'mensagem' => $dados['mensagem'],
                    'data' => $schedule,
                    'status' => $response->getStatusDescription(),
                    'evento' => 'Mensagem manual',
                    'tipo' => 'Enviada',
                    'user' => \Auth::user()->id
                ]);

                $user->update([
                    'sms_zenvia_qtd' => $user->sms_zenvia_qtd - 1
                ]);

                return response()->json('sucesso');
            }
            catch(\Exception $ex){

                MensagemSms::create([
                    'id_zenvia' => $id_sms,
                    'para' => '55'.preg_replace("/[^0-9]/", "", $dados['telefone']),
                    'mensagem' => $dados['mensagem'],
                    'data' => $schedule,
                    'status' => 'Erro',
                    'evento' => 'Mensagem manual',
                    'tipo' => 'Enviada',
                    'user' => \Auth::user()->id
                ]);

                return response()->json('Ocorreu algum erro, verifique os dados informados');
            }
        }
        else{

            return response()->json('Você não possui mensagens disponíveis!');
        }

    }

    public function historico(){

        $compras = CompraUsuario::where('comprador',\Auth::user()->id)->orderBy('id','DESC');

        return HistoricoSmsResource::collection($compras->paginate());

    }

    public function index(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $sms = ZenviaSms::where('projeto',Hashids::decode($request->id_projeto));

        return SmsProjetosResource::collection($sms->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados['projeto'] = $projeto['id'];

        ZenviaSms::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $sms = ZenviaSms::select('plano','evento','tempo','periodo','status','mensagem')
                        ->where('id',Hashids::decode($request->id_sms))->first();

        return response()->json($sms);
    }

    public function update(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados = $request->all();

        ZenviaSms::find(Hashids::decode($dados['id']))->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        ZenviaSms::find(Hashids::decode($request->id_sms))->delete();

        return response()->json('sucesso');
    }

    public function isAuthorized($id_projeto){

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projeto_usuario){
            return false;
        }

        return true;
    }
}
