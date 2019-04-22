<?php

namespace Modules\Parceiros\Transformers;

use App\User;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ParceirosResource extends Resource {

    public function toArray($request) {

        $user = User::find($this->user);
        if($user){
            $nome = $user['name'];
        }
        else{
            $nome = '';
        }

        return [
            'id' => Hashids::encode($this->id),
            'nome' => $nome,
            'tipo' => $this->tipo,
            'status' => $this->status,   
        ];
    }
}
