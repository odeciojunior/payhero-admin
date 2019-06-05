<?php

namespace Modules\Sms\Http\Controllers;

use DateTimeZone;
use Zenvia\Model\Sms;
use App\Entities\Plan;
use App\Entities\ZenviaSms;
use Zenvia\Model\SmsFacade;
use App\Entities\SmsMessage;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use NotificationChannels\Zenvia\Zenvia;
use Yajra\DataTables\Facades\DataTables;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;

class SmsController extends Controller {

    public function index() {

        $amountAvailable = \Auth::user()->sms_zenvia_qtd;

        $userProjects = UserProject::where([
            ['user',\Auth::user()->id],
            ['type','producer']
        ])->get()->toArray();

        $planosUsuario = [];

        foreach($userProjects as $userProject){
            $planos = Plan::where('project',$userProject['project'])->pluck('id')->toArray();
            if(count($planos) > 0){
                foreach($planos as $plano){
                    $planosUsuario[] = $plano;
                }
            }
        }

        $qtdSmsEnviados = SmsMessage::whereIn('plan',$planosUsuario)->where('type','Enviada')->count();

        $qtdSmsRecebidos = SmsMessage::whereIn('plan',$planosUsuario)->where('type','Recebida')->count();

        // $compras = CompraUsuario::where('comprador',\Auth::user()->id)->orderBy('id','DESC')->get()->toArray();

        foreach($compras as &$compra){
            $compra['data_inicio'] = date('d/m/Y',strtotime($compra['data_inicio']));
            if($compra['status'] == 'paid')
                $compra['status'] = 'Paga';
            elseif($compra['status'] == 'waiting_payment')
                $compra['status'] = 'Aguardando pagamento';
        }

        return view('sms::index',[
            'sms_disponiveis' => $amountAvailable,
            'sms_enviados'    => $qtdSmsEnviados,
            'sms_recebidos'   => $qtdSmsRecebidos,
            'compras'         => []
        ]);
    }
 
    public function cadastro() {

        return view('sms::cadastro');
    }
 
    public function cadastrarSms(Request $request){

        $dados = $request->all();
        $dados['project'] = Hashids::decode($dados['projeto'])[0];

        if($dados['time'] == ''){
            $dados['time'] = '0';
        }

        if($dados['time'] == '1'){
            if($dados['period'] == 'minutes')
                $dados['period'] = 'minute';
            elseif($dados['period'] == 'hours')
                $dados['period'] = 'hour';
            elseif($dados['period'] == 'days')
                $dados['period'] = 'day';
        }

        if($dados['plan'] == 'all'){
            unset($dados['plan']);
        }

        ZenviaSms::create($dados);

        return response()->json('Sucesso');
    }

    public function editarSms($id){

        $sms = ZenviaSms::find($id);

        return view('sms::editar',[
            'pixel' => $pixel,
        ]);
    }

    public function updateSms(Request $request){

        $dados = $request->all();

        unset($dados['projeto']);

        if($dados['time'] == ''){
            $dados['time'] = '0';
        }

        if($dados['time'] == '1'){
            if($dados['period'] == 'minutes')
                $dados['period'] = 'minute';
            elseif($dados['period'] == 'hours')
                $dados['period'] = 'hour';
            elseif($dados['period'] == 'days')
                $dados['period'] = 'day';
        }

        if($dados['plan'] == 'all'){
            unset($dados['plan']);
        }

        $sms = ZenviaSms::where('id',Hashids::decode($dados['id']))->first();
        $sms->update($dados);

        return response()->json('Sucesso');
    }

    public function deletarSms($id){

        $sms = ZenviaSms::where('id',Hashids::decode($id))->first();
        $sms->delete();

        return response()->json('sucesso');

    }

    public function dadosSms(Request $request) {

        $dados = $request->all();

        $sms = \DB::table('zenvia_sms as sms')
                    ->leftJoin('plans', 'plans.id', 'sms.plan');

        if(isset($dados['projeto'])){
            $sms = $sms->where('sms.project','=', Hashids::decode($dados['projeto']));
        }
        else{
            return response()->json('projeto não encontrado');
        }

        $sms = $sms->get([
                'sms.id',
                'sms.event',
                'sms.time',
                'sms.period',
                'sms.message',
                'sms.status',
                'plans.name as plan'
        ]);

        return Datatables::of($sms)
        ->editColumn('plan', function ($sms) {
            if($sms->plan == ''){
                return 'Todos plans';
            }
            return $sms->plan;
        })
        ->addColumn('detalhes', function ($sms) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_sms' data-placement='top' data-toggle='tooltip' title='Editar' sms='".Hashids::encode($sms->id)."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_sms' data-placement='top' data-toggle='tooltip' title='Excluir' sms='".Hashids::encode($sms->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesSms(Request $request){

        $dados = $request->all();

        $sms = ZenviaSms::where('id',Hashids::decode($dados['id_sms']))->first();

        $plano = Plan::find($sms->plano);

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Plano:</b></td>";
        $modalBody .= "<td>".$plano['nome']."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Evento:</b></td>";
        $modalBody .= "<td>".$sms->evento."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Tempo:</b></td>";
        $modalBody .= "<td>".$sms->tempo." ".$sms->periodo."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if($sms->status)
            $modalBody .= "<td>Ativo</td>";
        else
            $modalBody .= "<td>Inativo</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<td><b>Mensagem:</b></td>";
        $modalBody .= "<td>".$sms->mensagem."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function getFormAddSms(Request $request){

        $dados = $request->all();

        $plans = Plan::where('project', Hashids::decode($dados['projeto'])[0])->get()->toArray();

        foreach($plans as &$plan){
            if($plan['description'] != ''){
                $plan['name'] = $plan['name'] . ' - ' . $plan['description'];
            }
        }

        $form = view('sms::cadastro',[
            'plans' => $plans
        ]);

        return response()->json($form->render());

    }

    public function getFormEditarSms(Request $request){

        $dados = $request->all();

        $plans = Plan::where('project', $dados['projeto'])->get()->toArray();

        $sms = ZenviaSms::where('id',Hashids::decode($dados['id']))->first();

        $idSms = Hashids::encode($sms->id);

        $form = view('sms::editar',[
            'sms_id' => $idSms,
            'sms'    => $sms,
            'plans'  => $plans
        ]);

        return response()->json($form->render());

    }

    public function detalhesCompra(Request $request){

        $dados = $request->all();

        $compra = CompraUsuario::find($dados['id_compra']);

        if($compra['status'] == 'paid')
            $compra['status'] = 'Paga';
        if($compra['status'] == 'waiting_payment')
            $compra['status'] = 'Aguardando pagamento';

        $detalhes = "
            <tr>
                <td><b>Forma de pagamento</b></td>
                <td>".$compra['forma_pagamento']."</td>
            </tr>
        ";
        if($compra['forma_pagamento'] == "Boleto"){
            $detalhes .= "
                <tr>
                    <td><b>Link do boleto</b></td>
                    <td>".$compra['link_boleto']."</td>
                </tr>
            ";
        }
        $detalhes .= "
            <tr>
                <td><b>Quantidade</b></td>
                <td>".$compra['quantidade']."</td>
            </tr>
            <tr>
            <td><b>Valor</b></td>
                <td>R$ ".$compra['valor_total_pago']."</td>
            </tr>
            <tr>
                <td><b>Status</b></td>
                <td>".$compra['status']."</td>
            </tr>
            <tr>
                <td><b>Data da compra</b></td>
                <td>".date('d/m/Y',strtotime($compra['data_inicio']))."</td>
            </tr>
        ";            
        if($compra['forma_pagamento'] == "Boleto"){
            $detalhes .= "
                <tr>
                    <td><b>Data de pagamento</b></td>
                    <td>".date('d/m/Y',strtotime($compra['data_inicio']))."</td>
                </tr>
            ";
        }

        return response()->json($detalhes);
    }

    public function smsAtendimento(Request $request){

        $dados = $request->all();

        $smsFacade = new SmsFacade('healthlab.corp','hLQNVb7VQk');
        try {
            $response = $smsFacade->listMessagesReceived();

            if ($response->hasMessages()) {
                $messages = $response->getReceivedMessages();
                foreach ($messages as $smsReceived) {

                    $mensagem_enviada = SmsMessage::where('id_zenvia',$smsReceived->getSmsOriginId())->first();

                    SmsMessage::create([
                        'id_zenvia' => $smsReceived->getSmsOriginId(),
                        'plano'     => @$mensagem_enviada->plano,
                        'para'      => $smsReceived->getMobile(),
                        'mensagem'  => $smsReceived->getBody(),
                        'data'      => $smsReceived->getDateReceived(),
                        'status'    => 'Ok',
                        'evento'    => 'Mensagem recebida',
                        'tipo'      => 'Recebida'
                    ]);

                }
            } 
        } catch (\Exception $ex) {
            //
        }

        return view("sms::sms");

    }

    public function dadosMensagens(Request $request){

        $userProjetos = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']
        ])->get()->toArray();

        $planosUsuario = [];

        foreach($userProjetos as $userProjeto){

            $planos = Plan::where('projeto',$userProjeto['projeto'])->pluck('id')->toArray();

            if(count($planos) > 0){
                foreach($planos as $plano){
                    $planosUsuario[] = $plano;
                }
            }
        }

        $mensagens = \DB::table('mensagens_sms as mensagem')
        ->leftJoin('planos as plano', 'plano.id', 'mensagem.plano')
        ->whereIn('plano.id',$planosUsuario)
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

        return Datatables::of($mensagens)
        ->editColumn('evento', function ($mensagem) {
            if($mensagem->evento == 'venda_realizada')
                return "Venda realizada";
            if($mensagem->evento == 'boleto_gerado')
                return "Boleto gerado";
            if($mensagem->evento == 'boleto_vencido')
                return "Boleto vencido";

            return $mensagem->evento;
        })
        ->editColumn('para', function ($mensagem) {
            $qtd_numeros = (strlen($mensagem->para) -4);
            $string = "(%s%s)";
            for($x = 0; $x < $qtd_numeros; $x++){
                $string .= "%s";
            }
            return vsprintf($string, str_split(substr($mensagem->para, 2)));
        })
        ->editColumn('data', function ($mensagem) {

            return date_format(date_create($mensagem->data),"d/m/Y H:i:s");
        })
        ->make(true);

    }

    public static function enviarSmsManual(Request $request){

        $user = \Auth::user();

        if($user->sms_zenvia_qtd > 0){
            $dados = $request->all();

            $smsFacade = new SmsFacade('healthlab.corp','hLQNVb7VQk');
            $sms = new Sms();
            $sms->setTo('55'.preg_replace("/[^0-9]/", "", $dados['telefone']));
            $sms->setMsg($dados['mensagem']);
            $idSms = uniqid();
            $sms->setId($idSms);
            $sms->setCallbackOption(Sms::CALLBACK_NONE);
            $date = new \DateTime();
            $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
            $schedule = $date->format("Y-m-d\TH:i:s");
            $sms->setSchedule($schedule);

            try{
                $response = $smsFacade->send($sms);

                SmsMessage::create([
                    'id_zenvia' => $idSms,
                    'para'      => '55'.preg_replace("/[^0-9]/", "", $dados['telefone']),
                    'mensagem'  => $dados['mensagem'],
                    'data'      => $schedule,
                    'status'    => $response->getStatusDescription(),
                    'evento'    => 'Mensagem manual',
                    'tipo'      => 'Enviada',
                    'user'      => \Auth::user()->id
                ]);

                $user->update([
                    'sms_zenvia_qtd' => $user->sms_zenvia_qtd - 1
                ]);

                return response()->json('Sucesso');
            }
            catch(\Exception $ex){

                SmsMessage::create([
                    'id_zenvia' => $idSms,
                    'para'      => '55'.preg_replace("/[^0-9]/", "", $dados['telefone']),
                    'mensagem'  => $dados['mensagem'],
                    'data'      => $schedule,
                    'status'    => 'Erro',
                    'evento'    => 'Mensagem manual',
                    'tipo'      => 'Enviada',
                    'user'      => \Auth::user()->id
                ]);

                return response()->json('Ocorreu algum erro, verifique os dados informados!');
            }
        }
        else{

            return response()->json('Você não possui mensagens disponíveis!');
        }
    }

}


