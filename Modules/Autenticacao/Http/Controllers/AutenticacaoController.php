<?php

namespace Modules\Autenticacao\Http\Controllers;

use DB;
use App\Entities\User;
use App\OauthAccessToken;
use GuzzleHttp\Client;
use Lcobucci\JWT\Parser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AutenticacaoController extends Controller {

    public function login(Request $request) {

        $request->validate([
            'email'    => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'status'  =>'error', 
                'message' =>'Usuário não encontrado'
            ])->header('Access-Control-Allow-Origin', '*');
        }

        if(Hash::check($request->password, $user->password)){

            $http = new Client;

            $response = $http->post(url('oauth/token'),[
                'form_params' => [
                    'grant_type'    => 'password',
                    'client_id'     => '4',
                    'client_secret' => 'PACFiT34wcfycuyK5LKHgoTHr8Ueex02B8sMDQNm',
                    'username'      => $request->email,
                    'password'      => $request->password,
                ]
            ]);

            return response()->json([
                'data' => json_decode((string) $response->getBody(), true)
            ])->header('Access-Control-Allow-Origin', '*');
        }

        return response()->json([
            'status'  => 'error', 
            'message' => 'Dados inválidos'
        ])->header('Access-Control-Allow-Origin', '*');

    }

    public function logout(Request $request) {

        $value = $request->bearerToken();

        if ($value) {
            $id = (new Parser())->parse($value)->getHeader('jti');
            $revoked = DB::table('oauth_access_tokens')->where('id', '=', $id)->update(['revoked' => 1]);
        }

        Auth::logout();

        return response()->json('sucesso');
    }

}


