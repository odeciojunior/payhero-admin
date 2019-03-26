<?php

namespace Modules\Planos\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PlanosResource extends Resource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'codigo' => $this->cod_identificador,
            'preco' => $this->preco,
            'foto' => $this->foto
        ];
    }
}
