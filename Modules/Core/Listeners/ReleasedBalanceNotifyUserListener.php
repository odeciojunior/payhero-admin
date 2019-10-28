<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\ReleasedBalanceEvent;
use Modules\Core\Services\UserNotificationService;
use Modules\Notifications\Notifications\ReleasedBalanceNotification;

class ReleasedBalanceNotifyUserListener
{
    /**
     * @var string
     * @description name of the column in user_notifications table to check if it will send
     */
    private $userNotification = "released_balance";
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param ReleasedBalanceEvent $event
     */
    public function handle(ReleasedBalanceEvent $event)
    {
        try {
            $userModel = new User();

            $transfers = $event->transfer;

            $transfers = $transfers->groupBy('user_id')->map(function($row) {
                return $row->sum('value');
            });

            $message = '';
            foreach ($transfers as $user_id => $value) {
                $user    = $userModel->find($user_id);
                $message = 'O valor de R$' . number_format(intval($value) / 100, 2, ',', '.') . ' foi acrescentado ao saldo disponível.';

                /** @var UserNotificationService $userNotificationService */
                $userNotificationService = app(UserNotificationService::class);
                if ($userNotificationService->verifyUserNotification($user, $this->userNotification)) {
                    $user->notify(new ReleasedBalanceNotification($message));
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar notificação saldo liberado');
            report($e);
        }
    }
}
