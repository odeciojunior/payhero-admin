<?php

namespace Modules\Notifications\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Transformers\NotificationResource;
use Throwable;

/**
 * Class NotificationsApiController
 * @package Modules\Notifications\Http\Controllers
 */
class NotificationsApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markasread(Request $request)
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();

            return response()->json(['message' => 'sucesso'], 200);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @return JsonResponse|RedirectResponse
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
            report($e);
        }
    }

    /**
     * @return AnonymousResourceCollection
     * @throws Throwable
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

            return NotificationResource::collection($notifications);
        } catch (Exception $e) {
            report($e);
        }
    }
}
