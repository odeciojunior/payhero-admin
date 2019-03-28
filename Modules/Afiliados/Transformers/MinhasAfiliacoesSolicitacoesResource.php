<?php

namespace Modules\Afiliados\Transformers;

use App\Projeto;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MinhasAfiliacoesSolicitacoesResource extends Resource {

    public function toArray($request) {

        $projeto = Projeto::find($this->projeto);

        return [
            'id' => Hashids::encode($this->id),
            'projeto' => $projeto['nome'],
            'status' => $this->status,
            'created_at' => $this->created_at
        ];

    }

}
