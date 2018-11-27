<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\ZenviaSms;
use NotificationChannels\Zenvia\ZenviaChannel;
use NotificationChannels\Zenvia\ZenviaMessage;
use NotificationChannels\Zenvia\Zenvia;

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

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index() {

        return view('sms::index'); 
    }

    /**
     * Display a form to store new users.
     * @return Response
     */
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

        ZenviaSms::find($dados['smsData']['id'])->update($dados['smsData']);

        return response()->json('Sucesso');
    }

    public function deletarSms($id){

        ZenviaSms::find($id)->delete();

        return response()->json('sucesso');

    }

    /**
     * Return data for datatable
     */
    public function dadosSms(Request $request) {

        $dados = $request->all();

        $sms = \DB::table('zenvia_sms as sms');

        if(isset($dados['projeto'])){
            $sms = $sms->where('sms.projeto','=', $dados['projeto']);
        }

        $sms = $sms->get([
                'sms.id',
                'sms.evento',
                'sms.tempo',
                'sms.mensagem',
        ]);

        return Datatables::of($sms)
        ->addColumn('detalhes', function ($sms) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_sms' data-placement='top' data-toggle='tooltip' title='Detalhes' sms='".$sms->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
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

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Evento:</b></td>";
        $modal_body .= "<td>".$sms->evento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Tempo:</b></td>";
        $modal_body .= "<td>".$sms->tempo."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Período:</b></td>";
        $modal_body .= "<td>".$sms->periodo."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
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

    public function getFormAddSms(){

        $form = view('sms::cadastro');

        return response()->json($form->render());

    }

    public function getFormEditarSms(Request $request){

        $dados = $request->all();

        $sms = ZenviaSms::find($dados['id']);

        $form = view('sms::editar',[
            'sms' => $sms,
        ]);

        return response()->json($form->render());

    }

}
