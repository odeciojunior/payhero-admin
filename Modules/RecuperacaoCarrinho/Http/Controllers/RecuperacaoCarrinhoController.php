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
use Modules\RecuperacaoCarrinho\Transformers\CarrinhosAbandonadosResource;

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
            'checkout.projeto',
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
                return "<span class='badge badge-danger'>Não recuperado</span>";
            }
            else{
                return "<span class='badge badge-success'>Recuperado</span>";
            }
        })
        ->addColumn('valor', function ($checkout) {
            $valor = 0;
            $planosCheckout = PlanoCheckout::where('checkout',$checkout->id)->get()->toArray();
            foreach($planosCheckout as $plano_checkout){
                $plano = Plano::find($plano_checkout['plano']);
                $valor += str_replace('.','',$plano['preco']) * $plano_checkout['quantidade'];
            }
            return substr_replace($valor, '.',strlen($valor) - 2, 0 );;
        })
        ->addColumn('link', function ($checkout) {

            $dominio = Dominio::where('projeto',$checkout->projeto)->first();

            return "https://checkout.".$dominio['dominio']."/carrinho/".$checkout->id_sessao_log;
        })
        ->rawColumns(['detalhes','status_recuperacao'])
        ->make(true);
    }

}
