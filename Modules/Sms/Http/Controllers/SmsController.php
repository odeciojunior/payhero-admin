<?php

namespace Modules\Sms\Http\Controllers;

use App\Entities\Plan;
use App\Entities\SmsMessage;
use App\Entities\ZenviaSms;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Zenvia\Zenvia;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Zenvia\Model\Sms;
use Zenvia\Model\SmsFacade;

class SmsController extends Controller
{
    private $smsMessageModel;
    private $plansModel;
    private $zenviaSms;

    private function getSmsMessage()
    {
        if (!$this->smsMessageModel) {
            $this->smsMessageModel = app(SmsMessage::class);
        }

        return $this->smsMessageModel;
    }

    public function getPlans()
    {
        if (!$this->plansModel) {
            $this->plansModel = app(Plan::class);
        }

        return $this->plansModel;
    }

    public function getZenviaSms()
    {
        if (!$this->zenviaSms) {
            $this->zenviaSms = app(ZenviaSms::class);
        }

        return $this->zenviaSms;
    }

    /*public function index()
    {

        $amountAvailable = \Auth::user()->sms_zenvia_qtd;

        $userProjects = UserProject::where([
                                               ['user', \Auth::user()->id],
                                               ['type', 'producer'],
                                           ])->get()->toArray();

        $planosUsuario = [];

        foreach ($userProjects as $userProject) {
            $planos = Plan::where('project', $userProject['project'])->pluck('id')->toArray();
            if (count($planos) > 0) {
                foreach ($planos as $plano) {
                    $planosUsuario[] = $plano;
                }
            }
        }

        $qtdSmsEnviados = SmsMessage::whereIn('plan', $planosUsuario)->where('type', 'Enviada')->count();

        $qtdSmsRecebidos = SmsMessage::whereIn('plan', $planosUsuario)->where('type', 'Recebida')->count();

        // $compras = CompraUsuario::where('comprador',\Auth::user()->id)->orderBy('id','DESC')->get()->toArray();

        foreach ($compras as &$compra) {
            $compra['data_inicio'] = date('d/m/Y', strtotime($compra['data_inicio']));
            if ($compra['status'] == 'paid')
                $compra['status'] = 'Paga';
            else if ($compra['status'] == 'waiting_payment')
                $compra['status'] = 'Aguardando pagamento';
        }

        return view('sms::index', [
            'sms_disponiveis' => $amountAvailable,
            'sms_enviados'    => $qtdSmsEnviados,
            'sms_recebidos'   => $qtdSmsRecebidos,
            'compras'         => [],
        ]);
    }*/

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $dados = $request->all();

            if (isset($dados['projectId'])) {
                $projectId = Hashids::decode($dados['projectId'])[0];
                $sms       = $this->getZenviaSms()->with([
                                                             'plan' => function($query) use ($projectId) {
                                                                 $query->where('project', $projectId);
                                                             },
                                                         ])->where('project', $projectId)->get();
            } else {
                return response()->json('projeto não encontrado');
            }

            return Datatables::of($sms)
                             ->editColumn('plan', function($sms) {
                                 if ($sms->plan == '') {
                                     return 'Todos plans';
                                 }
                                 $plan = $this->getPlans()->find($sms->plan);

                                 return $plan->name;
                             })
                             ->addColumn('detalhes', function($sms) {
                                 return "<span data-toggle='modal' data-target='#modal_detalhes'>
                                            <a class='btn btn-outline btn-success detalhes_sms' data-placement='top' data-toggle='tooltip' title='Detalhes' sms='" . Hashids::encode($sms->id) . "'>
                                                <i class='icon wb-order' aria-hidden='true'></i>
                                            </a>
                                        </span>
                                        <span data-toggle='modal' data-target='#modal_editar'>
                                            <a class='btn btn-outline btn-primary editar_sms' data-placement='top' data-toggle='tooltip' title='Editar' sms='" . Hashids::encode($sms->id) . "'>
                                                <i class='icon wb-pencil' aria-hidden='true'></i>
                                            </a>
                                        </span>
                                        <span data-toggle='modal' data-target='#modal_excluir'>
                                            <a class='btn btn-outline btn-danger excluir_sms' data-placement='top' data-toggle='tooltip' title='Excluir' sms='" . Hashids::encode($sms->id) . "'>
                                                <i class='icon wb-trash' aria-hidden='true'></i>
                                            </a>
                                        </span>";
                             })->rawColumns(['detalhes'])->make(true);
        } catch (Exception $e) {
            Log::warning("Erro ao tentar acessar dados (SmsController - index)");
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        try {
            $projectId = $request->input('project');
            if ($projectId) {
                $projectId = Hashids::decode($projectId)[0];
                $plans     = $this->getPlans()->where('project', $projectId)->get();
            }

            return view('sms::cadastro', ['plans' => $plans]);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para pagina de cadastro de sms (SmsController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data            = $request->all();
            $data['project'] = Hashids::decode($data['project'])[0];

            if ($data['time'] == '') {
                $dat['time'] = '0';
            }

            if ($data['time'] == '1') {
                if ($data['period'] == 'minutes') {
                    $data['period'] = 'minute';
                } else if ($data['period'] == 'hours') {
                    $data['period'] = 'hour';
                } else if ($data['period'] == 'days') {
                    $data['period'] = "day";
                }
            }

            if ($data['plan'] == 'all') {
                $data['plan'] = null;
            }

            $zenviaSmsSaved = $this->getZenviaSms()->create($data);

            if ($zenviaSmsSaved) {
                return response()->json('Sucesso');
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar sms (SmsController - store)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {

            $data = $request->all();
            if (isset($data['smsId'])) {
                $idSms = Hashids::decode($data['smsId'])[0];
                $sms   = $this->getZenviaSms()->find($idSms);

                if (!$sms) {

                    return response()->json('Erro não foi possivel encontrar os dados');
                }

                $plan      = $this->getPlans()->find($sms->plan);
                $modalBody = '';

                $modalBody .= "<div class='col-xl-12 col-lg-12'>";
                $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
                $modalBody .= "<thead>";
                $modalBody .= "</thead>";
                $modalBody .= "<tbody>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Plano:</b></td>";
                if ($plan)
                    $modalBody .= "<td>" . $plan->name . "</td>";
                else
                    $modalBody .= "<td>  </td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Evento:</b></td>";
                $modalBody .= "<td>" . $sms->event . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Tempo:</b></td>";
                $modalBody .= "<td>" . $sms->time . " " . $sms->period . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Status:</b></td>";

                if ($sms->status)
                    $modalBody .= "<td>Ativo</td>";
                else
                    $modalBody .= "<td>Inativo</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<td><b>Mensagem:</b></td>";
                $modalBody .= "<td>" . $sms->message . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "</thead>";
                $modalBody .= "</table>";
                $modalBody .= "</div>";
                $modalBody .= "</div>";

                return response()->json($modalBody);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados (SmsController - show)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['project'])) {
                $projectId = Hashids::decode($data['project'])[0];
                $sms       = Hashids::decode($data['id'])[0];
                $plans     = $this->getPlans()->where('project', $projectId)->get();
                $sms       = $this->getZenviaSms()->find($sms);

                $view = view("sms::editar", [
                    'sms_id' => $data['id'],
                    'sms'    => $sms,
                    'plans'  => $plans,
                ]);

                return response()->json($view->render());
            }

            return response()->json('Dados não encontrados');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela de editar sms');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $data = $request->input('smsData');

            if (isset($data)) {
                if ($data['time'] == '') {
                    $data['time'] = '0';
                }

                if ($data['time'] == '1') {
                    if ($data['period'] == 'minutes') {
                        $data['period'] = 'minute';
                    } else if ($data['period'] == 'hours') {
                        $data['period'] = 'hour';
                    } else if ($data['period'] == 'days') {
                        $data['period'] = 'day';
                    }
                }

                if ($data['plan'] == 'all') {
                    $data['plan'] = null;
                } else {
                    $data['plan'] = Hashids::decode($data['plan'])[0];
                }

                $smsId      = Hashids::decode($data['id'])[0];
                $sms        = $this->getZenviaSms()->find($smsId);
                $smsUpdated = $sms->update($data);
                if ($smsUpdated) {
                    return response()->json('Sucesso');
                }
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar sms (SmsController - update)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $zenviaSmsId      = Hashids::decode($id)[0];
            $zenviaSms        = $this->getZenviaSms()->where('id', $zenviaSmsId)->first();
            $zenviaSmsDeleted = $zenviaSms->delete();
            if ($zenviaSmsDeleted) {
                return response()->json('Sucesso');
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar deletar zenviSms (SmsController - destroy)');
            report($e);
        }
    }

    public function editarSms($id)
    {

        $sms = ZenviaSms::find($id);

        return view('sms::editar', [
            'pixel' => $pixel,
        ]);
    }

    public function getFormAddSms(Request $request)
    {

        $dados = $request->all();

        $plans = Plan::where('project', Hashids::decode($dados['projeto'])[0])->get()->toArray();

        foreach ($plans as &$plan) {
            if ($plan['description'] != '') {
                $plan['name'] = $plan['name'] . ' - ' . $plan['description'];
            }
        }

        $form = view('sms::cadastro', [
            'plans' => $plans,
        ]);

        return response()->json($form->render());
    }

    public function detalhesCompra(Request $request)
    {

        $dados = $request->all();

        $compra = CompraUsuario::find($dados['id_compra']);

        if ($compra['status'] == 'paid')
            $compra['status'] = 'Paga';
        if ($compra['status'] == 'waiting_payment')
            $compra['status'] = 'Aguardando pagamento';

        $detalhes = "
            <tr>
                <td><b>Forma de pagamento</b></td>
                <td>" . $compra['forma_pagamento'] . "</td>
            </tr>
        ";
        if ($compra['forma_pagamento'] == "Boleto") {
            $detalhes .= "
                <tr>
                    <td><b>Link do boleto</b></td>
                    <td>" . $compra['link_boleto'] . "</td>
                </tr>
            ";
        }
        $detalhes .= "
            <tr>
                <td><b>Quantidade</b></td>
                <td>" . $compra['quantidade'] . "</td>
            </tr>
            <tr>
            <td><b>Valor</b></td>
                <td>R$ " . $compra['valor_total_pago'] . "</td>
            </tr>
            <tr>
                <td><b>Status</b></td>
                <td>" . $compra['status'] . "</td>
            </tr>
            <tr>
                <td><b>Data da compra</b></td>
                <td>" . date('d/m/Y', strtotime($compra['data_inicio'])) . "</td>
            </tr>
        ";
        if ($compra['forma_pagamento'] == "Boleto") {
            $detalhes .= "
                <tr>
                    <td><b>Data de pagamento</b></td>
                    <td>" . date('d/m/Y', strtotime($compra['data_inicio'])) . "</td>
                </tr>
            ";
        }

        return response()->json($detalhes);
    }

    public function smsAtendimento(Request $request)
    {

        $dados = $request->all();

        $smsFacade = new SmsFacade('healthlab.corp', 'hLQNVb7VQk');
        try {
            $response = $smsFacade->listMessagesReceived();

            if ($response->hasMessages()) {
                $messages = $response->getReceivedMessages();
                foreach ($messages as $smsReceived) {

                    $mensagem_enviada = SmsMessage::where('id_zenvia', $smsReceived->getSmsOriginId())->first();

                    SmsMessage::create([
                                           'id_zenvia' => $smsReceived->getSmsOriginId(),
                                           'plano'     => @$mensagem_enviada->plano,
                                           'para'      => $smsReceived->getMobile(),
                                           'mensagem'  => $smsReceived->getBody(),
                                           'data'      => $smsReceived->getDateReceived(),
                                           'status'    => 'Ok',
                                           'evento'    => 'Mensagem recebida',
                                           'tipo'      => 'Recebida',
                                       ]);
                }
            }
        } catch (\Exception $ex) {
            //
        }

        return view("sms::sms");
    }

    public function dadosMensagens(Request $request)
    {

        $userProjetos = UserProjeto::where([
                                               ['user', \Auth::user()->id],
                                               ['tipo', 'produtor'],
                                           ])->get()->toArray();

        $planosUsuario = [];

        foreach ($userProjetos as $userProjeto) {

            $planos = Plan::where('projeto', $userProjeto['projeto'])->pluck('id')->toArray();

            if (count($planos) > 0) {
                foreach ($planos as $plano) {
                    $planosUsuario[] = $plano;
                }
            }
        }

        $mensagens = \DB::table('mensagens_sms as mensagem')
                        ->leftJoin('planos as plano', 'plano.id', 'mensagem.plano')
                        ->whereIn('plano.id', $planosUsuario)
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
                                 ])->orderBy('mensagem.id', 'DESC');

        return Datatables::of($mensagens)
                         ->editColumn('evento', function($mensagem) {
                             if ($mensagem->evento == 'venda_realizada')
                                 return "Venda realizada";
                             if ($mensagem->evento == 'boleto_gerado')
                                 return "Boleto gerado";
                             if ($mensagem->evento == 'boleto_vencido')
                                 return "Boleto vencido";

                             return $mensagem->evento;
                         })
                         ->editColumn('para', function($mensagem) {
                             $qtd_numeros = (strlen($mensagem->para) - 4);
                             $string      = "(%s%s)";
                             for ($x = 0; $x < $qtd_numeros; $x++) {
                                 $string .= "%s";
                             }

                             return vsprintf($string, str_split(substr($mensagem->para, 2)));
                         })
                         ->editColumn('data', function($mensagem) {

                             return date_format(date_create($mensagem->data), "d/m/Y H:i:s");
                         })
                         ->make(true);
    }

    public static function enviarSmsManual(Request $request)
    {

        $user = \Auth::user();

        if ($user->sms_zenvia_qtd > 0) {
            $dados = $request->all();

            $smsFacade = new SmsFacade('healthlab.corp', 'hLQNVb7VQk');
            $sms       = new Sms();
            $sms->setTo('55' . preg_replace("/[^0-9]/", "", $dados['telefone']));
            $sms->setMsg($dados['mensagem']);
            $idSms = uniqid();
            $sms->setId($idSms);
            $sms->setCallbackOption(Sms::CALLBACK_NONE);
            $date = new \DateTime();
            $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
            $schedule = $date->format("Y-m-d\TH:i:s");
            $sms->setSchedule($schedule);

            try {
                $response = $smsFacade->send($sms);

                SmsMessage::create([
                                       'id_zenvia' => $idSms,
                                       'para'      => '55' . preg_replace("/[^0-9]/", "", $dados['telefone']),
                                       'mensagem'  => $dados['mensagem'],
                                       'data'      => $schedule,
                                       'status'    => $response->getStatusDescription(),
                                       'evento'    => 'Mensagem manual',
                                       'tipo'      => 'Enviada',
                                       'user'      => \Auth::user()->id,
                                   ]);

                $user->update([
                                  'sms_zenvia_qtd' => $user->sms_zenvia_qtd - 1,
                              ]);

                return response()->json('Sucesso');
            } catch (\Exception $ex) {

                SmsMessage::create([
                                       'id_zenvia' => $idSms,
                                       'para'      => '55' . preg_replace("/[^0-9]/", "", $dados['telefone']),
                                       'mensagem'  => $dados['mensagem'],
                                       'data'      => $schedule,
                                       'status'    => 'Erro',
                                       'evento'    => 'Mensagem manual',
                                       'tipo'      => 'Enviada',
                                       'user'      => auth()->user()->id,
                                   ]);

                return response()->json('Ocorreu algum erro, verifique os dados informados!');
            }
        } else {

            return response()->json('Você não possui mensagens disponíveis!');
        }
    }
}

