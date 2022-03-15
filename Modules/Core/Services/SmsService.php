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
    public function sendSms($number, $message, $sender = '', $msgType = 'DisparoPro')
    {
        try {
            /*
                $zenvia = new ZenviaSmsService();
                $zenvia->sendSms($number, $message);
                $easySms = new EasySendSmsService($number, $message, $sender, $msgType);
                $easySms->submit();
            */

            if (getenv('APP_ENV') != 'production') {
                $number = getenv('APP_NUMBER_PHONE_TEST');
            }

            $number = preg_replace("/[^0-9]/", "", $number);
            if (strlen($number) == 11) {
                $number = '55' . $number;
            }

            if($msgType == 'aws-sns')
                AwsSns::sendMessage($number, $message);
            else
                DisparoProService::sendMessage($number, $message);

            return true;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
}
