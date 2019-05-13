<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Plano;
use App\Venda;
use App\Empresa;
use App\Projeto;
use App\Transacao;
use Carbon\Carbon;
use Pusher\Pusher;
use App\PlanoVenda;
use PagarMe\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class DashboardController extends Controller {

    public function index() {

        $saldoDisponivel = 0;
        $saldoFuturo = 0;

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        foreach($empresas as $empresa){

            $transacoesAguardando = Transacao::where('empresa',$empresa['id'])
                ->where('status','pago')
                ->whereDate('data_liberacao', '>', Carbon::today()->toDateString())
                ->get()->toArray();

            if(count($transacoesAguardando)){

                foreach($transacoesAguardando as $transacao){

                    $saldoFuturo += $transacao['valor'];

                }
            }
        }

        if($saldoDisponivel == 0){
            $saldoDisponivel = '000';
        }
        if($saldoFuturo == 0){
            $saldoFuturo = '000';
        }

        $saldoDisponivel = \Auth::user()->saldo;
        $saldoDisponivel = number_format($saldoDisponivel,2);
        $saldoFuturo = substr_replace($saldoFuturo, '.',strlen($saldoFuturo) - 2, 0 );
        $saldoFuturo = number_format($saldoFuturo,2);

        return view('dashboard::dashboard',[
            'saldo_disponivel' => $saldoDisponivel,
            'saldo_futuro' => $saldoFuturo
        ]);

    }

    public function ultimasVendas(Request $request){

        $dados = $request->all();

        $vendas = Venda::select('id','data_inicio','valor_total_pago','forma_pagamento','ip')
        ->where([
            [ 'proprietario', \Auth::user()->id ],
            [ 'pagamento_status', '!=', 'refused']
        ])->orderBy('id', 'DESC')
        ->limit(10)
        ->get()->toArray();

        foreach($vendas as &$venda){
            $planoVenda = PlanoVenda::where('venda',$venda['id'])->first();
            $plano = Plano::find($planoVenda->plano);
            $projeto = Projeto::find($plano['projeto']);
            $venda['projeto'] = $projeto['nome'];
            $venda['data_inicio'] = (new Carbon($venda['data_inicio']))->format('d/m/Y H:i:s');
        }

        return response()->json($vendas);
    }

}
