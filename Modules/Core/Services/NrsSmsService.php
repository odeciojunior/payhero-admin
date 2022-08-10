<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class SmsService
 * @package Modules\Core\Services
 */
class NrsSmsService
{
    public static function sendMessage($number, $textMessage)
    {
        $post["to"] = [$number];
        $post["text"] = $textMessage;
        $post["from"] = "Cloufox Teste";
        $user = "joaolucas102030";
        $password = "9c3mkm3UxUg2W2X";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gateway.plusmms.net/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Authorization: Basic " . base64_encode($user . ":" . $password),
        ]);
        $result = curl_exec($ch);

        dd($result);
    }
}
