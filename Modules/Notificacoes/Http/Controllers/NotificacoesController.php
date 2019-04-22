<?php

namespace Modules\Notificacoes\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class NotificacoesController extends Controller {

    public function markasread(Request $request) {

        \Auth::user()->unreadNotifications->markAsRead();
        return response()->json('sucesso');

    }

    public function qtdNotificacoes(){

        return response()->json([
            'qtd_notificacoes' => count(\Auth::user()->unreadNotifications)
        ]);

    }

    public function notificacoes(){

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
