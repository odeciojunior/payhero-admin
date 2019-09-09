<?php

namespace Modules\Core\Listeners;

use Exception;
use Modules\Core\Entities\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\ReleasedBalanceEvent;
use Modules\Notifications\Notifications\ReleasedBalanceNotification;

class ReleasedBalanceNotifyUserListener
{
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
                $user->notify(new ReleasedBalanceNotification($message));
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar notificação saldo liberado');
            report($e);
        }
    }
}
