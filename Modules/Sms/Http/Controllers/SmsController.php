<?php

namespace Modules\Sms\Http\Controllers;

use App\Plano;
use App\ZenviaSms;
use App\CompraUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use NotificationChannels\Zenvia\Zenvia;
use Yajra\DataTables\Facades\DataTables;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;

class SmsController extends Controller {

    public function enviarMensagem(){

        $dados = [
            'from'           => 'Cloudfox app',
            'msg'            => 'Cloudfox is growing up',
            'id'             => '26',
            'schedule'       => '2018-11-26T18:35:45',
            'callbackOption' => 'NONE',
            'flashSms'       => true
        ];

        $msg = new Zenvia('healthlab.corp','hLQNVb7VQk',null,false,32);

        $status = $msg->sendMessage('5555996931098', $dados);

        dd($status);
    }

    public function index() {

        $qtd_sms_disponiveis = \Auth::user()->sms_zenvia_qtd;

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
            'compras' => $compras
        ]); 
    }

    public function cadastro() {

        return view('sms::cadastro');
    }

    public function cadastrarSms(Request $request){

        $dados = $request->all();

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

}
