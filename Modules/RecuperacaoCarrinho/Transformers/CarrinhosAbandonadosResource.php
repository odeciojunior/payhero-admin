<?php

namespace Modules\RecuperacaoCarrinho\Transformers;

use App\Log;
use App\Plano;
use App\Dominio;
use Carbon\Carbon;
use App\PlanoCheckout;
use Illuminate\Http\Resources\Json\Resource;

class CarrinhosAbandonadosResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $comprador = '';
        $log = Log::where('id_sessao_log', $this->id_sessao_log)->orderBy('id','DESC')->first();
        if($log){
            $comprador = $log->nome;
        }

        $status = '';
        if($this->status == 'Carrinho abandonado'){
            $status = 'Não recuperado';
        }
        else{
            $status = 'Recuperado';
        }

        $valor = 0;
        $planos_checkout = PlanoCheckout::where('checkout',$this->id)->get()->toArray();
        foreach($planos_checkout as $plano_checkout){
            $plano = Plano::find($plano_checkout['plano']);
            $valor += str_replace('.','',$plano['preco']) * $plano_checkout['quantidade'];
        }
        $valor = substr_replace($valor, '.',strlen($valor) - 2, 0 );

        $dominio = Dominio::where('projeto',$this->projeto)->first();
        $link = "https://checkout.".$dominio['dominio']."/carrinho/".$this->id_sessao_log;

        return [
            'data' => with(new Carbon($this->created_at))->format('d/m/Y H:i:s'),
            'comprador' => $comprador,
            'status_email' => 'Não enviado',
            'status_sms' => 'Não enviado',
            'status_recuperacao' => $status,
            'valor' => $valor,
            'link' => $link
        ];

        return parent::toArray($request);
    }
}
