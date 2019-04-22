<?php

namespace Modules\Afiliados\Transformers;

use App\User;
use App\Projeto;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MeusAfiliadosResource extends Resource {

    public function toArray($request) {

        $afiliado = User::find($this->user);
        $projeto = Projeto::find($this->projeto);

        return [
            'id' => Hashids::encode($this->id),
            'afiliado' => $afiliado['name'],
            'projeto' => $projeto['nome'],
            'porcentagem' => $this->porcentagem,
            'created_at' => $this->created_at
        ];
    }

}
