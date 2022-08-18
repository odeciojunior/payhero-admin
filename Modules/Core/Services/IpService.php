<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;

class IpService
{
    public static function getRealIpAddr()
    {
        try {
            $ip = "";

            if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $ip = @$_SERVER["REMOTE_ADDR"];
            }
            return $ip;
        } catch (\Exception $e) {
            Log::warning("erro ao obter ip");
            report($e);
            return null;
        }
    }

    public static function getFirstValidIp($ip)
    {
        try {
            $explodeIp = explode(',', $ip);
            if (count($explodeIp) == 1) {
                $ip = current($explodeIp);
            } else if (count($explodeIp) == 2) {
                $ipv6 = $explodeIp[0] ?? null;
                $ipv4 = $explodeIp[1] ?? null;
                $ip   = $ipv4 ?? $ipv6;
            }

            return $ip;
        } catch (Exception $ex) {
            report($ex);

            return $ip;
        }
    }

}
