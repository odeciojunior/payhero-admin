<?php

namespace Modules\Notifications\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class NotificationsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markasread(Request $request)
    {
                auth()->user()->unreadNotifications->markAsRead();

        /*$unreadNotifications = count(auth()->user()->unreadNotifications);

        foreach ($unreadNotifications as $notification) {
            $unreadNotifications->markAsRead();
        }*/

        return response()->json('sucesso');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadNotificationsCount()
    {
        return response()->json([
                                    'qtd_notification' => count(auth()->user()->unreadNotifications),
                                ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getUnreadNotifications()
    {
        $view               = '';
        $countNotifications = count(auth()->user()->unreadNotifications);

        if ($countNotifications > 0) {
            $unreadNotifications = auth()->user()->unreadNotifications;

            foreach ($unreadNotifications as &$notification) {
                /*$notification->updated_at = Carbon::createFromFormat('d/m/Y H:m:s', $notification->updated_at);
                date('d/m/Y H:m:s', strtotime($notification->updated_at));*/
                $type = explode("\\", $notification->type);
                $view .= view('notifications::' . end($type), ['notification' => $notification])->render();
            }

            return response()->json([
                                        'notificacoes' => $view,
                                    ]);
        } else {
            return response()->json('null');
        }
    }
}
