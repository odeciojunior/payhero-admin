<?php

namespace Modules\Projetos\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProjetosResource extends Resource {

    public function toArray($request) {

        return [
            'id' => Hashids::encode($this->id),
            'foto' => $this->foto,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'created_at' => $this->created_at
        ];
    }
}
