<?php

namespace Modules\Notifications\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class NotificationsController
 * @package Modules\Notifications\Http\Controllers
 */
class NotificationsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markasread(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json('sucesso');
    }

    public function getUnreadNotificationsCount()
    {
        //
    }

    public function getUnreadNotifications()
    {
        //
    }
}
