<?php

namespace Modules\Notifications\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Notifications\Transformers\NotificationResource;


class NotificationsApiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markasread(Request $request)
    {
        try{
            auth()->user()->unreadNotifications->markAsRead();
    
            return response()->json(['message' => 'sucesso'], 200);
        }
        catch(Exception $e){
            Log::warning('Erro ao setar notificações como lidas');
            report($e);
        }
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
            Log::warning('Erro ao obter o count de notificações');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getUnreadNotifications()
    {
        try{
            $notifications = auth()->user()->unreadNotifications;
    
            if(count($notifications) < 10){
                $notificationsRead = auth()->user()->readNotifications()->take(10 - count($notifications))->orderBy('created_at', 'desc')->get();
                foreach($notificationsRead as $notificationRead){
                    $notifications->push($notificationRead);
                }
            }
    
            return NotificationResource::collection($notifications);
        }
        catch(Exception $e){
            Log::warning('Erro ao obter notificações');
            report($e);
        }
    }
}
