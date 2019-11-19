<?php

namespace Modules\Mobile\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            $dataRequest = $request->json()->all();

            $validator = Validator::make($dataRequest, [
                'email'             => 'required|string|email',
                'password'          => 'required|string',
                //'mobile_push_token' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Data',
                ], 400);
            } else {
                $credentials['email'] = $dataRequest['email'];
                $credentials['password'] = $dataRequest['password'];

                if (!Auth::attempt($credentials))
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized',
                    ], 401);

                $user = $request->user();
                $tokenResult = $user->createToken('personal_access_token', ['admin']);
                $token = $tokenResult->token;
                $token->save();

//                if (isset($dataRequest['mobile_push_token'])) {
//                    User::where('mobile_push_token', $dataRequest['mobile_push_token'])->update([
//                        'mobile_push_token' => null
//                    ]);
//
//                    User::where('id', $user->id)->update([
//                        'mobile_push_token' => $dataRequest['mobile_push_token']
//                    ]);
//
//                }

                return response()->json([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
            }
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
