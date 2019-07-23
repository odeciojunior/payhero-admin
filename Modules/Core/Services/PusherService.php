<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Pusher\PusherException;
use Vinkla\Hashids\Facades\Hashids;
use Exception;

class PusherService
{
    private $pusher;

    public function __construct()
    {
        try {
            $this->pusher = new Pusher(
                getenv('PUSHER_APP_KEY'),
                getenv('PUSHER_APP_SECRET'),
                getenv('PUSHER_APP_ID'),
                $this->getOptions()
            );
        } catch (Exception $e) {
            Log::warning('Erro ao instanciar pusher');
            report($e);
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'cluster' => getenv('PUSHER_APP_CLUSTER'),
            'useTLS'  => true,
        ];
    }

    /**
     * @param $data = []
     */
    public function sendPusher($data)
    {
        try {
            $this->pusher->trigger('channel-' . Hashids::connection('pusher_connection')
                                                       ->encode($data['user']), 'new-notification', $data);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar gera notificacao integracao shopify');
            report($e);
        }
    }
}
