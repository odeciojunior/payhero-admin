<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;

class IpService
{

    public static function getRealIpAddr()
    {

        try {
            $ip = '';

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = @$_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        } catch (\Exception $e) {
            Log::warning('erro ao obter ip');
            report($e);
            return null;
        }
    }


}
