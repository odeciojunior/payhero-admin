<?php

namespace Modules\Sms\Transformers;

use App\Plano;
use Illuminate\Http\Resources\Json\Resource;

class SmsProjetosResource extends Resource {

    public function toArray($request) {

        $plano = '';
        if($this->plano){
            $plano = Plano::find($this->plano);
            $plano = $plano['nome'];
        }
        else{
            $plano = 'Todos os planos';
        }

        return [
            'id' => $this->id,
            'evento' => $this->evento,
            'mensagem' => $this->mensagem,
            'tempo' => $this->tempo . ' ' . $this->periodo,
            'plano' => $plano     
        ];
    }
}
