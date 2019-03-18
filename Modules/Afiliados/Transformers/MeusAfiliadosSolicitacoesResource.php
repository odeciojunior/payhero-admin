<?php

namespace Modules\Afiliados\Transformers;

use App\User;
use App\Projeto;
use Illuminate\Http\Resources\Json\Resource;

class MeusAfiliadosSolicitacoesResource extends Resource {

    public function toArray($request) {

        $afiliado = User::find($this->user);
        $projeto = Projeto::find($this->projeto);

        return [
            'id' => $this->id,
            'afiliado' => $afiliado['nome'],
            'projeto' => $projeto['nome'],
            'porcentagem' => $projeto['porcentagem'],
            'created_at' => $this->created_at
        ];

    }

}
