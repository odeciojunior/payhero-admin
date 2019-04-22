<?php

namespace Modules\Relatorios\Transformers;

use App\Plano;
use App\Projeto;
use App\Comprador;
use Carbon\Carbon;
use App\PlanoVenda; 
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class VendasResource extends Resource {

    public function toArray($request) {

        $comprador = Comprador::find($this->comprador);

        $planos_venda = PlanoVenda::where('venda',$this->id)->get()->toArray();
        $produto = '';
        $projeto = '';
        foreach($planos_venda as $plano_venda){
            $plano = Plano::find($plano_venda['plano']);
            $produto = $plano['nome'];
            $projeto = Projeto::find($plano['projeto']);
            $projeto = $projeto['nome'];
        }
        if(count($planos_venda) > 1){
            $produto = "Carrinho";
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
            'total_pago' => $this->valor_total_pago,
        ];

    }
}
