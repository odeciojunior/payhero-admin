<?php 

namespace Modules\Core\Helpers;

use App\UserProjeto;

class AutorizacaoHelper {

    public static function isAuthorized($id_projeto){

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projeto_usuario){
            return false;
        }

        return true;
    }
}

