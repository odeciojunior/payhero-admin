<?php

namespace Modules\Afiliados\Transformers;

use App\Entities\User;
use App\Projeto;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MeusAfiliadosSolicitacoesResource extends Resource {

    public function toArray($request) {

        $afiliado = User::find($this->user);
        $projeto = Projeto::find($this->projeto);

        return [
            'id' => Hashids::encode($this->id),
            'afiliado' => $afiliado['nome'],
            'projeto' => $projeto['nome'],
            'porcentagem' => $projeto['porcentagem'],
            'created_at' => $this->created_at
        ];

    }

}
