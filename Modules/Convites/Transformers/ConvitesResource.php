<?php

namespace Modules\Convites\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ConvitesResource extends Resource {

    public function toArray($request) {

        return [
            'email_convidado' => $this->email_convidado,
            'status' => $this->status,
            'data_cadastro' => $this->data_cadastro,
            'data_expiracao' => $this->data_expiracao,
        ];
    }
}
