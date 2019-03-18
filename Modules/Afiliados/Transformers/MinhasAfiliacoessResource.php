<?php

namespace Modules\Afiliados\Transformers;

use App\Projeto;
use Illuminate\Http\Resources\Json\Resource;

class MinhasAfiliacoesResource extends Resource {

    public function toArray($request) {

        $projeto = Projeto::find($this->projeto);

        return [
            'id' => $this->id,
            'foto_projeto' => $projeto['foto'],
            'nome_projeto' => $projeto['nome'],
        ];
    }

}
