<?php 

namespace Modules\Core\Helpers;

use App\Entities\UserProjeto;

class AutorizacaoHelper {

    public static function isAuthorized($id_projeto){

        $projetoUsuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projetoUsuario){
            return false;
        }

        return true;
    }
}

