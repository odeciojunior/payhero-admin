<?php

namespace Modules\Shopify\Transformers;

use App\Projeto;
use Illuminate\Http\Resources\Json\Resource;

class IntegracoesShopifyResource extends Resource {

    public function toArray($request) {

        $projeto = Projeto::find($this->projeto);

        return [
            'id' => $this->id,
            'projeto_nome' => $projeto['nome'],
            'projeto_foto' => $projeto['foto']
        ];
    }
}
