<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class DisparoProService
 * @package Modules\Core\Services
 */
class DisparoProService
{
    /**
     * @param $number
     * @param $textMessage
     */
    public static function sendMessage($number, $textMessage)
    {
        try {

            $curl = curl_init();

            $detailsClient = json_encode([
                                             "numero"      => $number,
                                             "servico"     => "short",
                                             "mensagem"    => $textMessage,
                                             "codificacao" => "0",
                                         ]);

            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api.disparopro.com.br/mt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => "[
                        $detailsClient
                ]",
                CURLOPT_HTTPHEADER     => [
                    "authorization: Bearer " . getenv('DISPARO_PRO'),
                    "content-type:application/json",
                ],
            ]);

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            curl_close($curl);

            if ($err) {
                Log::info('cURL Error #: ' . $err);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar sms DisparoProService - SendMessage');
            report($e);
        }
    }
}
