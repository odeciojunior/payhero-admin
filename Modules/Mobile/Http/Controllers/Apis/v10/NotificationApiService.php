<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Transformers\NotificationResource;

/**
 * Class NotificationApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class NotificationApiService
{
    /**
     * NotificationApiService constructor.
     */
    public function __construct() { }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadNotifications()
    {
        try {
            $notifications = auth()->user()->unreadNotifications;

            if (count($notifications) < 10) {
                $notificationsRead = auth()->user()->readNotifications()->take(10 - count($notifications))
                                           ->orderBy('created_at', 'desc')->get();
                foreach ($notificationsRead as $notificationRead) {
                    $notifications->push($notificationRead);
                }
            }
            $notificationCollection = NotificationResource::collection($notifications);

            return response()->json(compact('notificationCollection'), 200);
        } catch (Exception $e) {
            Log::warning('Erro ao obter notificações');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao carregar as notificações - NotificationApiService - getUnreadNotifications',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return bool|string
     */
    public function sendMessage(Request $request)
    {
        $notificationChannel = [
            'nil'              => 'nil',
            'venda'            => '2a478392-e1b7-4e35-a80c-3942c893c20f',
            'boleto'           => '532e65cb-a690-4438-ab8e-8739d82eb4da',
            'notificacao'      => '803f7e0a-687b-40d5-9cd2-2972bc94fc19',
            'pedido_afiliacao' => '6cef3efa-2acf-43eb-b9d8-b5f90dcb1143',
        ];

        $headings = [
            "en" => $request['headings'],
        ];
        $content  = [
            "en" => $request['content'],
        ];
        $fields   = [
            'app_id'             => env('ONESIGNAL_APP_ID'),
            'android_channel_id' => $notificationChannel[$request['notification_sound']],
            'included_segments'  => $request['included_segments'],
            'contents'           => $content,
            'headings'           => $headings,
            'ios_sound'          => $request['notification_sound'] . '.wav',
        ];

        //        return $fields['included_segments'];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param Request $request
     */
    public function processPostback(Request $request)
    {
        try {

            $notificationMachine = new NotificationMachine();
            $notificationMachine->init($request);
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }
}
