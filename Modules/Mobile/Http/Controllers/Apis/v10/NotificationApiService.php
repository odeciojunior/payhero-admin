<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Illuminate\Support\Facades\Log;
use Modules\Notifications\Transformers\NotificationResource;

/**
 * Class NotificationApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class NotificationApiService {

    /**
     * NotificationApiService constructor.
     */
    public function __construct() { }

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
            $notificationCollection =  NotificationResource::collection($notifications);
            return response()->json(compact('notificationCollection'), 200);

        } catch (Exception $e) {
            Log::warning('Erro ao obter notificações');
            report($e);

            return response()->json([
                'message' => 'Erro ao carregar as notificações - NotificationApiService - getUnreadNotifications',
            ], 400);
        }
    }

}
