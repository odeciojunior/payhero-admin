<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDevice;

/**
 * Class FinanceApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class DeviceApiService
{
    /**
     * FinanceApiService constructor.
     */
    public function __construct() { }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeviceData(Request $request)
    {
        try {
            $user       = User::where('email', $request['email'])->first();
            $userDevice = UserDevice::where('player_id', $request['mobile_push_token'])->where('user_id', $user->id)
                                    ->first();

            return response()->json([
                                        'status'     => 'success',
                                        'deviceData' => $userDevice,
                                        'message'    => 'dados do device!',
                                    ], 200);
        } catch (Exception $ex) {
            Log::warning('Erro ao buscar dados do device (DeviceApiService - getDeviceUpdate)');
            report($ex);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotificationPermission(Request $request)
    {
        try {
            $user       = User::where('email', $request['email'])->first();
            $userDevice = UserDevice::where('player_id', $request['mobile_push_token'])->where('user_id', $user->id)
                                    ->first();
            $userDevice->update([
                                    'sale_notification'            => $request['sale_notification'],
                                    'billet_notification'          => $request['billet_notification'],
                                    'payment_notification'         => $request['payment_notification'],
                                    'withdraw_notification'        => $request['withdraw_notification'],
                                    'invitation_sale_notification' => $request['invitation_sale_notification'],
                                ]);

            return response()->json([
                                        'status'  => 'success',
                                        'message' => 'Permissões de notificações atualizadas!',
                                    ], 200);
        } catch (Exception $ex) {
            Log::warning('Erro ao atualizar permissão de notificação (DeviceApiService - updateNotificationPermission)');
            report($ex);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }
}
