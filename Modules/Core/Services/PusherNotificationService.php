<?php

namespace Modules\Core\Services;

use Exception;
use Log;
use Modules\Core\Entities\Sale;
use Pusher\Pusher;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PusherNotificationService
 * @package Modules\Core\Services
 */
class PusherNotificationService
{
    /**
     * @var array
     */
    private static $options = [
        'cluster' => 'us2',
        'useTLS'  => true,
    ];
    /**
     * @var string
     */
    private static $authKey = '339254dee7e0c0a31840';
    /**
     * @var string
     */
    private static $secret = '78ed93b50a3327693d20';
    /**
     * @var string
     */
    private static $appId = '724843';

    /**
     * @param Sale $sale
     * @return void
     */
    public static function userSaleChargeback(Sale $sale)
    {
        try {
            $sale->loadMissing(["user", "project"]);

            $pusher = new Pusher(
                self::$authKey,
                self::$secret,
                self::$appId,
                self::$options
            );

            $message = 'Chargeback na venda #' . Hashids::connection("sale_id")
                                                        ->encode($sale->id) . ' do projeto ' . $sale->project->name;

            $dataPusher = ['message' => $message];

            $pusher->trigger('channel-' . Hashids::connection('pusher_connection')
                                                 ->encode($sale->user->id), 'new-notification', $dataPusher);

            return;
        } catch (Exception $ex) {
            Log::warning('erro ao enviar notificação push de chargeback para o usuário na venda ' . $sale->id);
            report($ex);

            return;
        }
    }
}

