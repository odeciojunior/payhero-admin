<?php

namespace Modules\Authentication\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lcobucci\JWT\Parser;
use Illuminate\Http\Request;
use Modules\Core\Entities\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthenticationApiController
 * @package Modules\Authentication\Http\Controllers
 */
class AuthenticationApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não encontrado'
                ])->header('Access-Control-Allow-Origin', '*');
            }

            if (Hash::check($request->password, $user->password)) {

                $http = new Client;

                $response = $http->post(url('oauth/token'), [
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id' => '4',
                        'client_secret' => 'PACFiT34wcfycuyK5LKHgoTHr8Ueex02B8sMDQNm',
                        'username' => $request->email,
                        'password' => $request->password,
                    ]
                ]);

                return response()->json([
                    'data' => json_decode((string)$response->getBody(), true)
                ])->header('Access-Control-Allow-Origin', '*');
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Dados inválidos'
            ], 400);
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dados inválidos'
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $value = $request->bearerToken();

            if ($value) {
                $id = (new Parser())->parse($value)->getHeader('jti');
                $revoked = DB::table('oauth_access_tokens')->where('id', '=', $id)->update(['revoked' => 1]);
            }

            Auth::logout();

            return response()->json(['status' => 'success',
                'message' => 'Deslogado com sucesso']);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error',
                'message' => 'Erro ao deslogar'], 400);
        }
    }

}


