<?php

namespace Modules\Autenticacao\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class AutenticacaoController extends Controller {

    public function login(Request $request) {

        return response()->json('ok');

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'status'=>'error', 
                'message'=>'Usuário não encontrado'
            ]);
        }

        if(Hash::check($request->password, $user->password)){

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

            return response()->json([
                'data' => json_decode((string) $response->getBody(), true)
            ]);
        }

        return response()->json([
            'status'=>'error', 
            'message'=>'Dados inválidos'
        ]);

    }

}
