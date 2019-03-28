<?php

namespace Modules\Brindes\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class BrindesResource extends Resource {

    public function toArray($request) {

        return [
            'id' => Hashids::encode($this->id),
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo_brinde' => $this->tipo_brinde
        ];
    }
}
