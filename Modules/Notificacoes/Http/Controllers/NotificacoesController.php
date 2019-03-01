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

}
