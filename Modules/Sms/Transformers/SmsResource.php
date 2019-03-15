<?php

namespace Modules\Sms\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SmsResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $comprador = Comprador::find($this->comprador);

        $planos_venda = PlanoVenda::where('venda',$this->id)->get()->toArray();
        $produto = '';
        if(count($planos_venda) > 1){
            $produto = "Carrinho";
        }
        foreach($planos_venda as $plano_venda){
            $plano = Plano::find($plano_venda['plano']);
            $produto = $plano['nome'];
        }

        $status = '';
        if($this->pagamento_status == 'paid')
            $status = 'Aprovada';
        else if($this->pagamento_status == 'rejected')
            $status = 'Rejeitada';
        else if($this->pagamento_status == 'pending')
            $status = 'Pendente';
        else
            $status = $this->pagamento_status;

        return [
            'id' => $this->id,
            'produto' => $produto,
            'comprador' => $comprador['nome'],
            'forma' => $this->forma_pagamento == 'cartao_credito' ? 'cartão' : $this->forma_pagamento,
            'status' => $status,
            'data_inicio' => $this->data_inicio ? with(new Carbon($this->data_inicio))->format('d/m/Y H:i:s') : '',
            'data_finalizada' => $this->data_finalizada ? with(new Carbon($this->data_finalizada))->format('d/m/Y H:i:s') : '',
            'total_pago' => $this->valor_total_pago,
        ];
    }
}
