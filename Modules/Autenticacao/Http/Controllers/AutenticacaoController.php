<?php

namespace Modules\Autenticacao\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AutenticacaoController extends Controller {

    public function login(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response(['status'=>'error', 'message'=>'Usuário não encontrado']);
        }

        if(hash()->check($request->password, $user->password)){

            $http = new Client;

            $response = $http->post(url('oauth/token'),[
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => '4',
                    'client_secret' => 'PACFiT34wcfycuyK5LKHgoTHr8Ueex02B8sMDQNm',
                    'username' => $request->email,
                    'password' => $request->password,
                ]
            ]);

            return response(['data'=>json_decode((string) $response->getBody(), true)]);
        }

        return response(['status'=>'error', 'message'=>'Dados inválidos']);
    }

}
