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
     * @return bool
     */
    public function sendSms($number, $message)
    {
        try {
            /*
                $zenvia = new ZenviaSmsService();
                $zenvia->sendSms($number, $message);
                DisparoProService::sendMessage($number, $message);
            */
            $easySms = new EasySendSmsService($number, $message);
            $easySms->submit();

            return true;
        } catch (Exception $e) {
            Log::warning('erro ao enviar sms');
            report($e);

            return false;
        }
    }
}
