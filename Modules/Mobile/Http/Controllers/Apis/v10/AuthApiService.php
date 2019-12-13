<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lcobucci\JWT\Parser;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDevice;
use Modules\Core\Services\FoxUtils;
use stringEncode\Exception;

/**
 * Class AuthApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class AuthApiService
{
    /**
     * AuthApiService constructor.
     */
    public function __construct() { }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials['email']    = $request['email'];
            $credentials['password'] = $request['password'];

            if (!Auth::attempt($credentials)) {
                return response()->json([
                                            'status'  => 'success',
                                            'message' => 'Unauthorized',
                                        ], 401);
            }

            //TODO COMENTAR ESSA LINHA QUANDO NAO FOR TESTE
            if ($request['mobile_push_token'] == 'null') {
                $request['mobile_push_token'] = '66421f99-fbca-447c-ae0e-513275c9fe81';
            }

            $user = $request->user();

            $userDevice = UserDevice::where('player_id', $request['mobile_push_token'])->where('user_id', $user->id)
                                    ->first();
            //VERIFICA SE JA EXISTE UM USUARIO COM O MOBILE TOKEN, CASO NAO EXISTA, CRIA, SE EXISTIR APENAS ATUALIZA O STATUS PARA ONLINE.
            if (!FoxUtils::isEmpty($userDevice)) {
                UserDevice::where('player_id', $request['mobile_push_token'])->where('user_id', $user->id)
                          ->update(['online' => true]);
            } else {
                $deviceInformation = json_decode($this->getDeviceInformation($request), true);
                $createUserDevices = [
                    'user_id'                      => $user->id,
                    'player_id'                    => $request['mobile_push_token'],
                    'online'                       => true,
                    'identifier'                   => $deviceInformation['identifier'],
                    'session_count'                => $deviceInformation['session_count'],
                    'language'                     => $deviceInformation['language'],
                    'timezone'                     => $deviceInformation['timezone'],
                    'game_version'                 => $deviceInformation['game_version'],
                    'device_os'                    => $deviceInformation['device_os'],
                    'device_type'                  => $deviceInformation['device_type'],
                    'device_model'                 => $deviceInformation['device_model'],
                    'ad_id'                        => $deviceInformation['ad_id'],
                    'tags'                         => json_encode($deviceInformation['tags']),
                    'last_active'                  => $deviceInformation['last_active'],
                    'playtime'                     => $deviceInformation['playtime'],
                    'amount_spent'                 => $deviceInformation['amount_spent'],
                    'onsignal_created_at'          => $deviceInformation['created_at'],
                    'invalid_identifier'           => $deviceInformation['invalid_identifier'],
                    'badge_count'                  => $deviceInformation['badge_count'],
                    'sdk'                          => $deviceInformation['sdk'],
                    'test_type'                    => $deviceInformation['test_type'],
                    'ip'                           => $deviceInformation['ip'],
                    'external_user_id'             => $deviceInformation['external_user_id'],
                    'sale_notification'            => true,
                    'billet_notification'          => true,
                    'payment_notification'         => true,
                    'withdraw_notification'        => true,
                    'invitation_sale_notification' => true,
                ];
                UserDevice::create($createUserDevices);
            }
            $tokenResult = $user->createToken('personal_access_token', ['admin']);
            $token       = $tokenResult->token;
            $token->save();

            //if (isset($dataRequest['mobile_push_token'])) {
            //User::where('mobile_push_token', $dataRequest['mobile_push_token'])->update([
            //'mobile_push_token' => null
            //]);

            //User::where('id', $user->id)->update([
            //'mobile_push_token' => $dataRequest['mobile_push_token']
            //]);
            //}

            return response()->json([
                                        'access_token' => $tokenResult->accessToken,
                                        'token_type'   => 'Bearer',
                                        'email'        => $user->email,
                                        'name'         => $user->name,
                                    ]);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Dados inválidos',
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
                $id      = (new Parser())->parse($value)->getHeader('jti');
                $revoked = DB::table('oauth_access_tokens')->where('id', '=', $id)->update(['revoked' => 1]);
            }

            Auth::logout();

            return response()->json([
                                        'status'  => 'success',
                                        'message' => 'Deslogado com sucesso',
                                    ]);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao deslogar',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return bool|string
     */
    public function getDeviceInformation(Request $request)
    {
        $player_id = $request['mobile_push_token'];
        $ch        = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players/" . $player_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic YOUR_ONESIGNAL_APP_AUTH_KEY_HERE',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutDevice(Request $request)
    {
        try {
            //ATUALIZA O USER DEVICE PARA OFF QUANDO FOR EFETUADO O LOGOUT
            if (!FoxUtils::isEmpty($request[0])) {
                $user = User::where('email', $request[1])->first();
                UserDevice::where('player_id', $request[0])
                          ->where('user_id', $user->id)
                          ->update(['online' => false]);
            } else {
                return response()->json([
                                            'status'  => 'error',
                                            'message' => 'Não foi possivel desvincular o device.',
                                        ], 400);
            }

            return response()->json([
                                        'status'  => 'success',
                                        'message' => 'Deslogado com sucesso',
                                    ], 200);
        } catch (Exception $ex) {
            return response()->json([
                                        'status'  => 'error',
                                        'message' => 'Erro ao deslogar',
                                    ], 400);
        }
    }
}
