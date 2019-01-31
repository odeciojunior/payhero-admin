<?php

namespace Modules\Sms\Http\Controllers;

use App\Plano;
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
use NotificationChannels\Zenvia\Zenvia;
use Yajra\DataTables\Facades\DataTables;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;

class SmsController extends Controller {

    public function index() {

        $qtd_sms_disponiveis = \Auth::user()->sms_zenvia_qtd;

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

        $qtd_sms_enviados = MensagemSms::whereIn('plano',$planos_usuario)->where('tipo','Enviada')->count();

        $qtd_sms_recebidos = MensagemSms::whereIn('plano',$planos_usuario)->where('tipo','Recebida')->count();

        $compras = CompraUsuario::where('comprador',\Auth::user()->id)->orderBy('id','DESC')->get()->toArray();

        foreach($compras as &$compra){
            $compra['data_inicio'] = date('d/m/Y',strtotime($compra['data_inicio'])); 

            if($compra['status'] == 'paid')
                $compra['status'] = 'Paga';
            elseif($compra['status'] == 'waiting_payment')
                $compra['status'] = 'Aguardando pagamento';
        }

        return view('sms::index',[
            'sms_disponiveis' => $qtd_sms_disponiveis,
            'sms_enviados' => $qtd_sms_enviados,
            'sms_recebidos' => $qtd_sms_recebidos,
            'compras' => $compras
        ]);
    }

    public function cadastro() {

        return view('sms::cadastro');
    }
 
    public function cadastrarSms(Request $request){

        $dados = $request->all();

        if($dados['tempo'] == ''){
            $dados['tempo'] = '0';
        }

        if($dados['tempo'] == '1'){
            if($dados['periodo'] == 'minutes')
                $dados['periodo'] = 'minute';
            elseif($dados['periodo'] == 'hours')
                $dados['periodo'] = 'hour';
            elseif($dados['periodo'] == 'days')
                $dados['periodo'] = 'day';
        }

        if($dados['plano'] == 'todos'){
            $planos = Plano::where('projeto', $dados['projeto'])->get()->toArray();
            foreach($planos as $plano){
                $dados['plano'] = $plano['id'];
                ZenviaSms::create($dados);
            }
        }
        else{
            ZenviaSms::create($dados);
        }

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

        if($dados['tempo'] == ''){
            $dados['tempo'] = '0';
        }

        if($dados['tempo'] == '1'){
            if($dados['periodo'] == 'minutes')
                $dados['periodo'] = 'minute';
            elseif($dados['periodo'] == 'hours')
                $dados['periodo'] = 'hour';
            elseif($dados['periodo'] == 'days')
                $dados['periodo'] = 'day';
        }

        ZenviaSms::find($dados['id'])->update($dados);

        return response()->json('Sucesso');
    }

    public function deletarSms($id){

        ZenviaSms::find($id)->delete();

        return response()->json('sucesso');

    }

    public function dadosSms(Request $request) {

        $dados = $request->all();

        $sms = \DB::table('zenvia_sms as sms')
                    ->leftJoin('planos', 'planos.id', 'sms.plano');

        if(isset($dados['projeto'])){
            $sms = $sms->where('sms.projeto','=', $dados['projeto']);
        }

        $sms = $sms->get([
                'sms.id',
                'sms.evento',
                'sms.tempo',
                'sms.periodo',
                'sms.mensagem',
                'sms.status',
                'planos.nome as plano'
        ]);

        return Datatables::of($sms)
        ->addColumn('detalhes', function ($sms) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_sms' data-placement='top' data-toggle='tooltip' title='Editar' sms='".$sms->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_sms' data-placement='top' data-toggle='tooltip' title='Excluir' sms='".$sms->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesSms(Request $request){

        $dados = $request->all();

        $sms = ZenviaSms::find($dados['id_sms']);

        $plano = Plano::find($sms->plano);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Plano:</b></td>";
        $modal_body .= "<td>".$plano['nome']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Evento:</b></td>";
        $modal_body .= "<td>".$sms->evento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Tempo:</b></td>";
        $modal_body .= "<td>".$sms->tempo." ".$sms->periodo."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($sms->status)
            $modal_body .= "<td>Ativo</td>";
        else
            $modal_body .= "<td>Inativo</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<td><b>Mensagem:</b></td>";
        $modal_body .= "<td>".$sms->mensagem."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }

    public function getFormAddSms(Request $request){

        $dados = $request->all();

        $planos = Plano::where('projeto', $dados['projeto'])->get()->toArray();

        $form = view('sms::cadastro',[
            'planos' => $planos
        ]);

        return response()->json($form->render());

    }

    public function getFormEditarSms(Request $request){

        $dados = $request->all();

        $planos = Plano::where('projeto', $dados['projeto'])->get()->toArray();

        $sms = ZenviaSms::find($dados['id']);

        $form = view('sms::editar',[
            'sms' => $sms,
            'planos' => $planos
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

                    $mensagem_enviada = MensagemSms::where('id_zenvia',$smsReceived->getSmsOriginId())->first();

                    MensagemSms::create([
                        'id_zenvia' => $smsReceived->getSmsOriginId(),
                        'plano' => @$mensagem_enviada->plano,
                        'para' => $smsReceived->getMobile(),
                        'mensagem' => $smsReceived->getBody(),
                        'data' => $smsReceived->getDateReceived(),
                        'status' => 'Ok',
                        'evento' => 'Mensagem recebida',
                        'tipo' => 'Recebida'
                    ]);

                }
            } 
        } catch (\Exception $ex) {
            //
        }

        return view("sms::sms");

    }

    public function dadosMensagens(Request $request){

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

                return response()->json('Sucesso');
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

                return response()->json('Ocorreu algum erro, verifique os dados informados!');
            }
        }
        else{

            return response()->json('Você não possui mensagens disponíveis!');
        }
    }

}
