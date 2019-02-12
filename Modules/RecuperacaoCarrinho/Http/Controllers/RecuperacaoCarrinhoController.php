<?php

namespace Modules\RecuperacaoCarrinho\Http\Controllers;

use App\Log;
use App\Plano;
use App\Dominio;
use App\Projeto;
use App\Checkout;
use Carbon\Carbon;
use App\PlanoCheckout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class RecuperacaoCarrinhoController extends Controller {


    public function index() {

        return view('recuperacaocarrinho::index');
    }

    public function dadosCarrinhosAbandonados(Request $request){

        $checkouts = \DB::table('checkouts as checkout')
        ->select([
            'checkout.id',
            'checkout.status',
            'checkout.id_sessao_log',
            'checkout.created_at',
        ])
        ->where('status','Carrinho abandonado')
        ->orWhere('status', 'Recuperado')
        ->orderBy('id','DESC');

        return Datatables::of($checkouts)
        ->editColumn('created_at', function ($checkout) {
            return with(new Carbon($checkout->created_at))->format('d/m/Y H:i:s');
        })
        ->addColumn('comprador', function ($checkout) {
            $log = Log::where('id_sessao_log', $checkout->id_sessao_log)->orderBy('id','DESC')->first();
            if($log)
                return $log->nome;
            return '';
        })
        ->addColumn('status_email', function ($checkout) {
            return "Não enviado";
        })
        ->addColumn('status_sms', function ($checkout) {
            return "Não enviado";
        })
        ->addColumn('status_recuperacao', function ($checkout) {
            if($checkout->status == 'Carrinho abandonado'){
                return "Não recuperado";
            }
            else{
                return "Recuperado";
            }
        })
        ->addColumn('valor', function ($checkout) {
            $valor = 0;
            $planos_checkout = PlanoCheckout::where('checkout',$checkout->id)->get()->toArray();
            foreach($planos_checkout as $plano_checkout){
                $plano = Plano::find($plano_checkout['plano']);
                $valor += str_replace('.','',$plano['preco']) * $plano_checkout['quantidade'];
            }
            return substr_replace($valor, '.',strlen($valor) - 2, 0 );;
        })
        ->addColumn('detalhes', function ($checkout) {
            return "<span data-toggle='modal' data-target='#modal_opcoes'>
                        <a class='btn btn-outline btn-primary opcoes_checkout' data-placement='top' data-toggle='tooltip' title='Opções' checkout='".$checkout->id."'>
                            <i class='icon wb-plus' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function opcoes(Request $request){

        $dados = $request->all();

        $checkout = Checkout::find($dados['id_checkout']);
        $dominio = Dominio::where('projeto',$checkout['projeto'])->first();

        $link = "<div style='margin-top:50px' class='text-center'><b>LINK: <b> https://checkout.".$dominio['dominio']."/carrinho/".$checkout['id_sessao_log']."</div>";

        return response()->json($link);
    }

}
