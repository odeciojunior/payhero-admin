<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class SmsService
 * @package Modules\Core\Services
 */
class SmsService
{
    /**
     * @param $number
     * @param $message
     * @param string $sender
     * @param string $msgType
     * @return bool
     */
    public function sendSms($number, $message, $sender = '', $msgType = '1')
    {
        try {
            /*
                $zenvia = new ZenviaSmsService();
                $zenvia->sendSms($number, $message);
                $easySms = new EasySendSmsService($number, $message, $sender, $msgType);
                $easySms->submit();
            */
            $number = preg_replace("/[^0-9]/", "", $number);
            if (strlen($number) == 11) {
                $number = '55' . $number;
            }

            DisparoProService::sendMessage($number, $message);

            return true;
        } catch (Exception $e) {
            Log::warning('erro ao enviar sms');
            report($e);

            return false;
        }
    }
}
