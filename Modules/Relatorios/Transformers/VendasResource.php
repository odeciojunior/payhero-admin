<?php

namespace Modules\Relatorios\Transformers;

use App\Plano;
use App\Empresa;
use App\Projeto;
use App\Comprador;
use App\Transacao;
use Carbon\Carbon;
use App\PlanoVenda; 
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class VendasResource extends Resource {

    public function toArray($request) {

        $comprador = Comprador::find($this->comprador);

        $planosVenda = PlanoVenda::where('venda',$this->id)->get()->toArray();
        $produto = '';
        $projeto = '';
        foreach($planosVenda as $planoVenda){
            $plano = Plano::find($planoVenda['plano']);
            $produto = $plano['nome'];
            $projeto = Projeto::find($plano['projeto']);
            $projeto = $projeto['nome'];
        }
        if(count($planosVenda) > 1){
            $produto = "Carrinho";
        }

        $empresasUsuario = Empresa::where('user', \Auth::user()->id)->pluck('id');

        $transacao = Transacao::where('venda',$this->id)->whereIn('empresa',$empresasUsuario)->first();
        if($transacao){
            $valor = $transacao->valor;
        }
        else{
            $valor = '000';
        }

        return [
            'id' => '#'.$this->id,
            'projeto' => $projeto,
            'produto' => $produto,
            'comprador' => $comprador['nome'],
            'forma' => $this->forma_pagamento == 'cartao_credito' ? 'cartão' : $this->forma_pagamento,
            'status' => $this->pagamento_status,
            'data_inicio' => $this->data_inicio ? with(new Carbon($this->data_inicio))->format('d/m/Y H:i:s') : '',
            'data_finalizada' => $this->data_finalizada ? with(new Carbon($this->data_finalizada))->format('d/m/Y H:i:s') : '',
            'total_pago' => 'R$ ' . substr_replace($valor, '.', strlen($valor) - 2, 0 ),
        ];

    }
}
