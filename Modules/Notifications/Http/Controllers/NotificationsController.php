<?php

namespace Modules\Notifications\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
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

        return response()->json('sucesso');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadNotificationsCount()
    {
        try {

            if (empty(auth()->user())) {
                return redirect()->route('login'); 
            } else {

                return response()->json([
                                            'qtd_notification' => count(auth()->user()->unreadNotifications),
                                        ]);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar contar notificações do usuario');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getUnreadNotifications()
    {

        $unreadNotifications = auth()->user()->unreadNotifications;

        $notifications = $this->completeWithReadNotifications($countNotifications);

        if(count($notifications) < 10){
            $notifications->push(auth()->user()->readNotifications()->take(10 - $notificationAmount)->orderBy('created_at', 'desc')->get());
        }

        dd($notifications);
        return NotificationsResource::collection($notifications);
    }

}



// public function completeWithReadNotifications(int $notificationAmount)
// {
//     $readNotifications = auth()->user()->readNotifications()->take(8 - $notificationAmount)
//                                ->orderBy('created_at', 'desc')->get();

//     $view    = '';
//     $counter = 0;

//     if (count($readNotifications) > 0) {
//         foreach ($readNotifications as $notification) {
//             $type = explode("\\", $notification->type);
//             $view .= view('notifications::' . end($type), ['notification' => $notification])->render();
//             $counter++;
//             if ($counter >= 8 - $notificationAmount) {
//                 return $view;
//             }
//         }

//         return $view;
//     } else {
//         return null;
//     }
// }
// foreach ($unreadNotifications as &$notification) {
            //     /*$notification->updated_at = Carbon::createFromFormat('d/m/Y H:m:s', $notification->updated_at);
            //     date('d/m/Y H:m:s', strtotime($notification->updated_at));*/
            //     $type = explode("\\", $notification->type);
            //     $view .= view('notifications::' . end($type), ['notification' => $notification])->render();
            // }
