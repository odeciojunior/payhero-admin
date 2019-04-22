<?php

namespace Modules\Empresas\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EmpresasResource extends Resource {

    public function toArray($request) {

        $status = '';
        if($this->recipient_id != ''){
            $status = 'Ativa';
        }
        else{
            $status = "Inativa";
        }

        return [
            'id' => $this->id,
            'nome_fantasia' => $this->nome_fantasia,
            'cpf_cnpj' => $this->cnpj,
            'status_conta_bancaria' => $status
        ];

    }
}
