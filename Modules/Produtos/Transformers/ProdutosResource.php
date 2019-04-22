<?php

namespace Modules\Produtos\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProdutosResource extends Resource {

    public function toArray($request) {

        return [
            'id' => Hashids::encode($this->id),
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'foto' => $this->foto,
            'created_at' => $this->created_at
        ];
    }
}
