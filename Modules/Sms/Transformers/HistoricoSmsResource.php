<?php

namespace Modules\Sms\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class HistoricoSmsResource extends Resource {

    public function toArray($request) {

        if($this->status == 'paid')
            $status = 'Paga';
        elseif($this->status == 'waiting_payment')
            $status = 'Aguardando pagamento';
        else
            $status = $this->status;

        return [
            'id' => $this->id,
            'quantidade' => $this->quantidade,
            'data' => date('d/m/Y',strtotime($this->data_inicio)),
            'valor' => $this->valor_total_pago,
            'forma_pagamento' => $this->forma_pagamento,
            'status' => $status
        ];
    }


}
