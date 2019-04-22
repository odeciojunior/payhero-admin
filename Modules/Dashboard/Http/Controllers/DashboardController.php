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

        $saldo_disponivel = 0;
        $saldo_transferido = 0;
        $saldo_futuro = 0;

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        foreach($empresas as $empresa){

            $transacoes_liberadas = Transacao::where('empresa',$empresa['id'])
                ->where('status','pago')
                ->whereDate('data_liberacao', '<=', Carbon::today()->toDateString())
                ->get()->toArray();

            if(count($transacoes_liberadas)){

                foreach($transacoes_liberadas as $transacao){
                    if($transacao['tipo'] == 'entrada'){
                        $saldo_disponivel += $transacao['valor'];
                    }
                    else{
                        $saldo_disponivel -= $transacao['valor'];
                    }
                }
            }
        }

        foreach($empresas as $empresa){

            $transacoes_aguardando = Transacao::where('empresa',$empresa['id'])
                ->where('status','pago')
                ->whereDate('data_liberacao', '>', Carbon::today()->toDateString())
                ->get()->toArray();

            if(count($transacoes_aguardando)){

                foreach($transacoes_aguardando as $transacao){
                    if($transacao['tipo'] == 'entrada'){
                        $saldo_futuro += $transacao['valor'];
                    }
                    else{
                        $saldo_futuro -= $transacao['valor'];
                    }
                }
            }
        }

        if($saldo_disponivel == 0){
            $saldo_disponivel = '000';
        }
        if($saldo_transferido == 0){
            $saldo_transferido = '000';
        }
        if($saldo_futuro == 0){
            $saldo_futuro = '000';
        }

        $saldo_disponivel = substr_replace($saldo_disponivel, '.',strlen($saldo_disponivel) - 2, 0 );
        $saldo_disponivel = number_format($saldo_disponivel,2);
        $saldo_transferido = substr_replace($saldo_transferido, '.',strlen($saldo_transferido) - 2, 0 );
        $saldo_transferido = number_format($saldo_transferido,2);
        $saldo_futuro = substr_replace($saldo_futuro, '.',strlen($saldo_futuro) - 2, 0 );
        $saldo_futuro = number_format($saldo_futuro,2);

        return view('dashboard::dashboard',[
            'saldo_disponivel' => $saldo_disponivel,
            'saldo_transferido' => $saldo_transferido,
            'saldo_futuro' => $saldo_futuro
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
            $plano_venda = PlanoVenda::where('venda',$venda['id'])->first();
            $plano = Plano::find($plano_venda->plano);
            $projeto = Projeto::find($plano['projeto']);
            $venda['projeto'] = $projeto['nome'];
            $venda['data_inicio'] = (new Carbon($venda['data_inicio']))->format('d/m/Y H:i:s');
        }

        return response()->json($vendas);
    }

}
