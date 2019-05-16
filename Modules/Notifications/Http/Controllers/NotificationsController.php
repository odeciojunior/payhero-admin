<?php

namespace Modules\Notifications\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class NotificationsController extends Controller {

    public function markasread(Request $request) {

        \Auth::user()->unreadNotifications->markAsRead();

        return response()->json('sucesso');
    }

    public function getUnreadNotificationsCount(){

        return response()->json([
            'qtd_notificacoes' => count(\Auth::user()->unreadNotifications)
        ]);
    }

    public function getUnreadNotifications(){

        if(count(\Auth::user()->unreadNotifications) > 0){
            $unreadNotifications = \Auth::user()->unreadNotifications;
            foreach($unreadNotifications as &$notification){
                $notification->updated_at = date('d/m/Y H:m:s', strtotime($notification->updated_at));
            }
            return response()->json([
                'notificacoes' => $unreadNotifications
            ]);
        }
        else{
            return response()->json('null');
        }

    }

}
