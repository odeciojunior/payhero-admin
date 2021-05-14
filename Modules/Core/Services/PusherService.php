<?php

namespace Modules\Core\Services;

use Pusher\Pusher;
use Vinkla\Hashids\Facades\Hashids;
use Exception;

/**
 * Class PusherService
 * @package Modules\Core\Services
 */
class PusherService
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * PusherService constructor.
     */
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
            'useTLS' => true,
        ];
    }

    /**
     * @param $data = []
     */
    public function sendPusher($data)
    {
        try {
            $this->pusher->trigger(
                'channel-' . Hashids::connection('pusher_connection')
                    ->encode($data['user']),
                'new-notification',
                $data
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
